<?php

namespace src\notas\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use PDO;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\value_objects\CursoStgr;

/**
 * Reportes de "asignaturas pendientes": dado un grupo de personas, calcula
 * que alumnos tienen pendientes al menos N asignaturas, o bien devuelve la
 * lista de asignaturas que falta a una persona concreta.
 *
 * Sucesor de `apps/notas/model/AsignaturasPendientes.php` (eliminado).
 * Diferencias respecto del modelo legacy:
 *
 *   - Sin estado mutable compartido entre metodos ni getters con side effects.
 *   - API tipada: tramo via `CursoStgr`, parametros `int`, retornos con
 *     `array<int, ...>` keyed por `id_nom`.
 *   - Prepared statements para los parametros que llegan del caller.
 *   - La tabla temporal `tmp_xa_asignaturas` se crea una sola vez por
 *     instancia (antes se reconstruia en cada llamada).
 *   - `listarFaltantesPorPersona()` y `contarFaltantesPorPersona()` son dos
 *     metodos separados en vez de un unico metodo polimorfico regulado por
 *     un flag `$blista`.
 */
final class AsignaturasPendientes
{
    private PDO $pdo;
    private AsignaturaRepositoryInterface $asignaturaRepository;
    private string $tablaNotas;
    private ?string $tablaPersonas;
    private string $tablaAsignaturasTemp = 'tmp_xa_asignaturas';
    private bool $asignaturasTempCreated = false;

    /**
     * @var array<string, array{count: int, niveles: array<int, int>}>
     *   cache por rango `"desde-hasta"` para evitar N consultas al repo.
     */
    private array $asignaturasPorRango = [];

    /**
     * @param string|null $tablaPersonas Nombre (o expresion SQL) de la tabla
     *   de personas. Puede ser una tabla real (`p_numerarios`, `p_agregados`,
     *   `personas_dl`) o una `TEMP TABLE` creada por el caller. Si se omite,
     *   los metodos que la necesiten lanzaran `\RuntimeException`.
     * @param string|null $ambito Valor de `ConfigGlobal::mi_ambito()`.
     *   Se parametriza para poder tests deterministas; si es `null` se
     *   resuelve en runtime.
     */
    public function __construct(
        AsignaturaRepositoryInterface $asignaturaRepository,
        ?string $tablaPersonas = null,
        ?string $ambito = null,
    ) {
        $this->pdo = GlobalPdo::get('oDB');
        $this->asignaturaRepository = $asignaturaRepository;
        $this->tablaPersonas = $tablaPersonas;
        $ambito ??= ConfigGlobal::mi_ambito();
        $this->tablaNotas = $ambito === 'rstgr' ? 'publicv.e_notas' : 'e_notas_dl';
    }

    /**
     * Personas a las que les faltan `N` o menos asignaturas para terminar
     * el tramo. Devuelve para cada persona el numero de asignaturas que le
     * faltan (entre `0` y `N`).
     *
     * @return array<int, int> id_nom => numero de asignaturas faltantes
     */
    public function contarFaltantesPorPersona(int $numAsignaturasMaximas, CursoStgr $curso): array
    {
        $filas = $this->fetchFaltantesBrutos($numAsignaturasMaximas, $curso);
        $numCurso = $this->asignaturasDeRango($curso)['count'];

        $result = [];
        foreach ($filas as $row) {
            $result[(int)$row['id_nom']] = $numCurso - (int)$row['asignaturas'];
        }
        return $result;
    }

    /**
     * Personas a las que les faltan `N` o menos asignaturas y, para cada
     * una, la lista de `nombre_corto` de esas asignaturas.
     *
     * @return array<int, array<int, string>> id_nom => [nombre_corto, ...]
     */
    public function listarFaltantesPorPersona(int $numAsignaturasMaximas, CursoStgr $curso): array
    {
        $filas = $this->fetchFaltantesBrutos($numAsignaturasMaximas, $curso);

        $result = [];
        foreach ($filas as $row) {
            $idNom = (int)$row['id_nom'];
            $result[$idNom] = $this->asignaturasQueFaltanPersona($idNom, $curso);
        }
        return $result;
    }

    /**
     * Personas a las que les falta una asignatura concreta. El valor de
     * cada entrada es `1` (marca de presencia). Se mantiene keyed por
     * `id_nom` para compatibilidad con los consumidores.
     *
     * @return array<int, int> id_nom => 1
     */
    public function personasQueLesFaltaAsignatura(
        int $idAsignatura,
        CursoStgr $curso,
        int $idTipoAsignatura,
    ): array {
        if ($idAsignatura <= 0) {
            return [];
        }
        $this->requireTablaPersonas();

        $nivelesStgr = implode(',', $curso->nivelesStgr());
        $columna = $idTipoAsignatura === 8 ? 'id_nivel' : 'id_asignatura';

        $sql = "
            SELECT p.id_nom
            FROM {$this->tablaPersonas} p
            WHERE p.situacion = 'A'
              AND p.nivel_stgr IN ($nivelesStgr)
            EXCEPT
            SELECT n.id_nom
            FROM {$this->tablaNotas} n
            WHERE n.{$columna} = :id_asignatura
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_asignatura' => $idAsignatura]);

        $result = [];
        foreach ($stmt as $row) {
            if (!is_array($row) || !array_key_exists('id_nom', $row)) {
                continue;
            }
            $result[(int) $row['id_nom']] = 1;
        }
        return $result;
    }

    /**
     * Lista de `nombre_corto` de las asignaturas del tramo que a `$idNom`
     * le faltan por cursar.
     *
     * @return array<int, string>
     */
    public function asignaturasQueFaltanPersona(int $idNom, CursoStgr $curso): array
    {
        $this->createAsignaturasTemp();
        $niveles = $this->asignaturasDeRango($curso)['niveles'];
        if (empty($niveles)) {
            return [];
        }
        $inNiveles = implode(',', $niveles);

        // Asignaturas obligatorias que el alumno no tiene en notas (JOIN
        // con subquery filtrada por id_nom < 3000 -> asignaturas reales).
        $sqlObligatorias = "
            SELECT a.nombre_corto, notas.id_asignatura
            FROM {$this->tablaAsignaturasTemp} a
            LEFT JOIN (
                SELECT id_asignatura
                FROM {$this->tablaNotas}
                WHERE id_nom = :id_nom AND id_asignatura < 3000
            ) notas USING (id_asignatura)
            WHERE a.id_tipo != 8
              AND notas.id_asignatura IS NULL
              AND a.id_nivel IN ($inNiveles)
        ";

        // Opcionales: se contabilizan por nivel (id_asignatura > 3000).
        $sqlOpcionales = "
            SELECT a.nombre_corto, notas.id_nivel
            FROM {$this->tablaAsignaturasTemp} a
            LEFT JOIN (
                SELECT id_nivel
                FROM {$this->tablaNotas}
                WHERE id_nom = :id_nom AND id_asignatura > 3000
            ) notas USING (id_nivel)
            WHERE a.id_tipo = 8
              AND notas.id_nivel IS NULL
              AND a.id_nivel IN ($inNiveles)
        ";

        $sql = "($sqlObligatorias) UNION ($sqlOpcionales) ORDER BY 2";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_nom' => $idNom]);

        $result = [];
        foreach ($stmt as $row) {
            if (!is_array($row) || !isset($row['nombre_corto']) || !is_scalar($row['nombre_corto'])) {
                continue;
            }
            $result[] = (string) $row['nombre_corto'];
        }
        return $result;
    }

    /**
     * Total de asignaturas activas del tramo + niveles involucrados.
     *
     * @return array{count: int, niveles: array<int, int>}
     */
    private function asignaturasDeRango(CursoStgr $curso): array
    {
        [$desde, $hasta] = $curso->rangoNiveles();
        $clave = $desde . '-' . $hasta;
        if (isset($this->asignaturasPorRango[$clave])) {
            return $this->asignaturasPorRango[$clave];
        }

        $asignaturas = $this->asignaturaRepository->getAsignaturas(
            ['active' => 't', 'id_nivel' => "$desde,$hasta"],
            ['id_nivel' => 'BETWEEN']
        );

        $niveles = [];
        foreach ($asignaturas as $oAsignatura) {
            $niveles[] = (int)$oAsignatura->getId_nivel();
        }

        return $this->asignaturasPorRango[$clave] = [
            'count' => count($asignaturas),
            'niveles' => $niveles,
        ];
    }

    /**
     * @return array<int, array{id_nom: int|string, asignaturas: int|string}>
     */
    private function fetchFaltantesBrutos(int $numAsignaturasMaximas, CursoStgr $curso): array
    {
        $this->requireTablaPersonas();

        $rango = $this->asignaturasDeRango($curso);
        $numCurso = $rango['count'];
        $niveles = $rango['niveles'];
        if (empty($niveles)) {
            return [];
        }
        $inNiveles = implode(',', $niveles);
        $inNivelStgr = implode(',', $curso->nivelesStgr());
        $minAprobadas = $numCurso - $numAsignaturasMaximas;

        $sqlConAlgunaNota = "
            SELECT DISTINCT p.id_nom, Count(*) AS asignaturas,
                   p.apellido1, p.apellido2, p.nom
            FROM {$this->tablaPersonas} p
            LEFT JOIN {$this->tablaNotas} n USING (id_nom)
            WHERE p.situacion = 'A'
              AND id_nivel IN ($inNiveles)
              AND p.nivel_stgr IN ($inNivelStgr)
            GROUP BY p.id_nom, p.apellido1, p.apellido2, p.nom
            HAVING Count(*) >= :min_aprobadas AND Count(*) < :num_curso
        ";

        if ($minAprobadas < 1) {
            // Si se pide "X o menos", hay que incluir tambien a los que
            // todavia no tienen ninguna nota (LEFT JOIN que no matchea).
            $sqlSinNotas = "
                SELECT p.id_nom, 0 AS asignaturas,
                       p.apellido1, p.apellido2, p.nom
                FROM {$this->tablaPersonas} p
                LEFT JOIN {$this->tablaNotas} n USING (id_nom)
                WHERE p.situacion = 'A'
                  AND p.nivel_stgr IN ($inNivelStgr)
                  AND n.id_nom IS NULL
                GROUP BY p.id_nom, p.apellido1, p.apellido2, p.nom
            ";
            $sql = "($sqlConAlgunaNota) UNION ($sqlSinNotas) ORDER BY 3, 4, 5";
        } else {
            $sql = "$sqlConAlgunaNota ORDER BY 3, 4, 5";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'min_aprobadas' => $minAprobadas,
            'num_curso' => $numCurso,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Construye `tmp_xa_asignaturas` con el catalogo completo de asignaturas
     * activas (las asignaturas viven en la BD `comun`, replicamos aqui para
     * poder hacer `JOIN`/`UNION` con `e_notas_dl`).
     *
     * Se garantiza una unica creacion por instancia de servicio.
     */
    private function createAsignaturasTemp(): void
    {
        if ($this->asignaturasTempCreated) {
            return;
        }
        $tabla = $this->tablaAsignaturasTemp;

        $this->pdo->exec("DROP TABLE IF EXISTS $tabla");
        $this->pdo->exec("CREATE TEMP TABLE $tabla (
            id_asignatura integer,
            id_nivel integer,
            nombre_asignatura character varying(100) NOT NULL,
            nombre_corto character varying(23),
            creditos numeric(4,2),
            year character varying(3),
            id_sector smallint,
            active boolean DEFAULT true NOT NULL,
            id_tipo integer
        )");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS {$tabla}_nivel ON $tabla (id_nivel)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS {$tabla}_id_asignatura ON $tabla (id_asignatura)");

        $cAsignaturas = $this->asignaturaRepository->getAsignaturas(['active' => 'true']);
        $prep = $this->pdo->prepare("
            INSERT INTO $tabla VALUES (
                :id_asignatura, :id_nivel, :nombre_asignatura, :nombre_corto,
                :creditos, :year, :id_sector, :active, :id_tipo
            )
        ");

        $this->pdo->beginTransaction();
        try {
            foreach ($cAsignaturas as $oAsignatura) {
                $prep->execute([
                    'id_asignatura' => $oAsignatura->getId_asignatura(),
                    'id_nivel' => $oAsignatura->getId_nivel(),
                    'nombre_asignatura' => $oAsignatura->getNombre_asignatura(),
                    'nombre_corto' => $oAsignatura->getNombre_corto(),
                    'creditos' => $oAsignatura->getCreditos(),
                    'year' => $oAsignatura->getYear(),
                    'id_sector' => $oAsignatura->getId_sector(),
                    'active' => $oAsignatura->isActive(),
                    'id_tipo' => $oAsignatura->getId_tipo(),
                ]);
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        $this->asignaturasTempCreated = true;
    }

    private function requireTablaPersonas(): void
    {
        if ($this->tablaPersonas === null || $this->tablaPersonas === '') {
            throw new \RuntimeException(
                'AsignaturasPendientes: no se ha configurado tabla de personas'
            );
        }
    }
}
