<?php

declare(strict_types=1);

namespace src\personas\application;

use PDO;
use PDOException;
use RuntimeException;
use src\devel_db_admin\application\MigracionEjecucionUtiles;
use src\personas\domain\CpSacdFila;
use src\personas\infrastructure\persistence\postgresql\CpSacdContexto;
use src\personas\infrastructure\persistence\postgresql\CpSacdWriter;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Throwable;

/**
 * Reconcilia la copia `cp_sacd` (BD comun) con las tablas de origen de la BD
 * interior, en todos los esquemas o en uno concreto.
 *
 * Sirve para dos cosas:
 *   - el reset: dejar la copia igual al origen después de un periodo sin
 *     sincronización incremental (que es como quedó tras el refactor);
 *   - el detector de deriva: en modo informe (`$aplicar = false`) un resultado
 *     con cambios significa que la sincronización incremental está fallando.
 *
 * No se borra y recarga: se comparan las filas y sólo se escriben las que
 * difieren, para no generar ruido en la replicación hacia el exterior.
 *
 * Conexiones: usa el bloque `importar` (usuario de mantenimiento) igual que
 * {@see \src\devel_db_admin\application\MigracionesEjecutar}, porque el cron no
 * tiene sesión y hay que recorrer esquemas ajenos al del login.
 *
 * Sólo tiene sentido en la instalación **interior sv**: es donde viven las
 * tablas de origen. Ejecutarlo desde sf vaciaría la copia (sf comparte el
 * esquema de comun pero no tiene las personas). El driver CLI lo comprueba.
 */
final class ResincronizarCpSacd
{
    /** Tablas de origen por id_tabla, en el esquema interior de la dl. */
    private const TABLAS_DL = ['p_numerarios', 'p_agregados', 'p_sssc'];

    /** Las personas de paso viven en un esquema común a todas las dl. */
    private const ESQUEMA_DE_PASO = 'restov';
    private const TABLA_DE_PASO = 'p_de_paso_ex';

    /** Columnas que no existen en `p_de_paso_ex` (se copian como null). */
    private const COLUMNAS_SIN_DE_PASO = ['id_ctr', 'publicado_para'];

    /** Tope de personas detalladas por esquema en el informe. */
    private const MAX_DETALLE = 20;

    /** SQLSTATE de «la tabla no existe» / «el esquema no existe». */
    private const SQLSTATE_NO_EXISTE = ['42P01', '3F000'];

    public function __construct(
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly CpSacdWriter $writer,
    ) {
    }

    /**
     * @param bool   $aplicar      false = sólo informe (por defecto)
     * @param string $soloEsquema  esquema comun concreto (p. ej. `H-dlb`); vacío = todos
     * @return array{
     *     aplicado: bool,
     *     esquemas: list<array<string, mixed>>,
     *     totales: array{esquemas: int, altas: int, cambios: int, bajas: int, errores: int},
     *     lineas: list<string>
     * }
     */
    public function execute(bool $aplicar = false, string $soloEsquema = ''): array
    {
        $pdoComun = $this->conectar('public');
        $pdoInterior = $this->conectar('publicv');

        $esquemas = $this->esquemasComun($soloEsquema);

        $informes = [];
        $lineas = [];
        $totales = ['esquemas' => 0, 'altas' => 0, 'cambios' => 0, 'bajas' => 0, 'errores' => 0];

        foreach ($esquemas as $esquema => $id_schema) {
            $informe = $this->reconciliarEsquema($pdoComun, $pdoInterior, $esquema, $id_schema, $aplicar);
            $informes[] = $informe->toArray();

            if ($informe->esOmitido()) {
                $lineas[] = sprintf('  %-16s omitido (%s)', $esquema, $informe->omitido);
                continue;
            }

            $totales['esquemas']++;
            if ($informe->tieneError()) {
                $totales['errores']++;
                $lineas[] = sprintf('  %-16s ERROR: %s', $esquema, $informe->error);
                continue;
            }

            $totales['altas'] += $informe->altas;
            $totales['cambios'] += $informe->cambios;
            $totales['bajas'] += $informe->bajas;

            $lineas[] = sprintf(
                '  %-16s origen=%-4d copia=%-4d altas=%-3d cambios=%-3d bajas=%-3d%s',
                $esquema,
                $informe->origen,
                $informe->destino,
                $informe->altas,
                $informe->cambios,
                $informe->bajas,
                $aplicar ? '' : '  (informe)',
            );
        }

        return [
            'aplicado' => $aplicar,
            'esquemas' => $informes,
            'totales' => $totales,
            'lineas' => $lineas,
        ];
    }

    private function reconciliarEsquema(
        PDO $pdoComun,
        PDO $pdoInterior,
        string $esquema,
        int $id_schema,
        bool $aplicar,
    ): CpSacdInformeEsquema {
        $esquemav = $esquema . 'v';
        if (!MigracionEjecucionUtiles::esquemaExisteEnPostgres($pdoComun, $esquema)) {
            return CpSacdInformeEsquema::omitido($esquema, 'no existe en comun');
        }
        if (!MigracionEjecucionUtiles::esquemaExisteEnPostgres($pdoInterior, $esquemav)) {
            return CpSacdInformeEsquema::omitido($esquema, 'no existe en sv');
        }

        $dl = CpSacdContexto::dlDeEsquema($esquema);
        if ($dl === '') {
            return CpSacdInformeEsquema::omitido($esquema, 'no se puede deducir la dl');
        }

        $contexto = new CpSacdContexto($pdoComun, $esquema, $dl, $id_schema);

        try {
            $origen = $this->leerOrigen($pdoInterior, $esquemav, $dl);
            $destino = $this->writer->filasActuales($contexto);
            if ($destino === false) {
                return CpSacdInformeEsquema::conError($esquema, 'no se ha podido leer cp_sacd');
            }

            $altas = [];
            $cambios = [];
            $detalle = [];
            foreach ($origen as $id_nom => $fila) {
                if (!isset($destino[$id_nom])) {
                    $altas[$id_nom] = $fila;
                    continue;
                }
                $distintas = CpSacdFila::diferencias($fila, $destino[$id_nom]);
                if ($distintas !== []) {
                    $cambios[$id_nom] = $fila;
                    if (count($detalle) < self::MAX_DETALLE) {
                        $detalle[] = sprintf('%d: %s', $id_nom, implode(',', $distintas));
                    }
                }
            }
            $bajas = array_values(array_diff(array_keys($destino), array_keys($origen)));

            // Salvaguarda: si la copia estuviera compartida entre esquemas, no
            // tocar las filas de otra dl aunque no aparezcan en nuestro origen.
            $ajenas = $this->writer->idNomsDeOtroEsquema($contexto);
            if ($ajenas !== []) {
                $bajas = array_values(array_diff($bajas, $ajenas));
                $detalle[] = sprintf('%d fila(s) de otro id_schema respetadas', count($ajenas));
            }

            if ($aplicar && ($altas !== [] || $cambios !== [] || $bajas !== [])) {
                $this->aplicarCambios($contexto, $altas + $cambios, $bajas);
            }

            return CpSacdInformeEsquema::reconciliado(
                $esquema,
                count($origen),
                count($destino),
                count($altas),
                count($cambios),
                count($bajas),
                $detalle,
            );
        } catch (Throwable $e) {
            return CpSacdInformeEsquema::conError($esquema, $e->getMessage());
        }
    }

    /**
     * Todo el esquema en una transacción: o queda reconciliado, o se queda como estaba.
     *
     * @param array<int, array<string, mixed>> $upserts
     * @param list<int> $bajas
     */
    private function aplicarCambios(CpSacdContexto $contexto, array $upserts, array $bajas): void
    {
        $pdoComun = $contexto->pdoComun;
        $pdoComun->beginTransaction();
        try {
            foreach ($upserts as $fila) {
                if ($this->writer->upsert($contexto, $fila) === false) {
                    throw new RuntimeException('upsert: ' . $this->writer->getErrorTxt());
                }
            }
            if ($bajas !== [] && $this->writer->eliminarVarios($contexto, $bajas) === false) {
                throw new RuntimeException('delete: ' . $this->writer->getErrorTxt());
            }
            $pdoComun->commit();
        } catch (Throwable $e) {
            if ($pdoComun->inTransaction()) {
                $pdoComun->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Filas que **deberían** estar en la copia de esta dl.
     *
     * @return array<int, array<string, mixed>> id_nom => fila
     */
    private function leerOrigen(PDO $pdoInterior, string $esquemav, string $dl): array
    {
        $filas = [];

        foreach (self::TABLAS_DL as $tabla) {
            foreach ($this->consultarOrigen($pdoInterior, $esquemav, $tabla, null) as $registro) {
                $fila = CpSacdFila::desdeRegistro($registro);
                if (CpSacdFila::debeCopiarse($fila, $dl)) {
                    $filas[CpSacdFila::idNom($fila)] = $fila;
                }
            }
        }

        foreach ($this->consultarOrigen($pdoInterior, self::ESQUEMA_DE_PASO, self::TABLA_DE_PASO, $dl) as $registro) {
            $fila = CpSacdFila::desdeRegistro($registro);
            if (CpSacdFila::debeCopiarse($fila, $dl)) {
                $filas[CpSacdFila::idNom($fila)] = $fila;
            }
        }

        return $filas;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function consultarOrigen(PDO $pdo, string $esquema, string $tabla, ?string $dl): array
    {
        $dePaso = $dl !== null;
        $columnas = [];
        foreach (CpSacdFila::COLUMNAS as $columna) {
            if ($dePaso && in_array($columna, self::COLUMNAS_SIN_DE_PASO, true)) {
                $columnas[] = 'NULL AS ' . $columna;
                continue;
            }
            $columnas[] = $columna;
        }

        $sql = 'SELECT ' . implode(',', $columnas)
            . ' FROM ' . self::comillas($esquema) . '.' . self::comillas($tabla)
            . ' WHERE sacd IS TRUE';
        $parametros = [];
        if ($dePaso) {
            $sql .= ' AND dl = :dl';
            $parametros['dl'] = $dl;
        }

        try {
            $stmt = $pdo->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('no se ha podido preparar la consulta de origen');
            }
            $stmt->execute($parametros);

            $registros = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $registro) {
                if (!is_array($registro)) {
                    continue;
                }
                $normalizado = [];
                foreach ($registro as $clave => $valor) {
                    $normalizado[(string) $clave] = $valor;
                }
                $registros[] = $normalizado;
            }

            return $registros;
        } catch (PDOException $e) {
            // Tabla o esquema inexistentes (instalaciones antiguas): no es un error.
            // Cualquier otro fallo sí lo es: si lo tragáramos, el origen parecería
            // vacío y en modo --aplicar se borraría la copia entera del esquema.
            if (in_array((string) $e->getCode(), self::SQLSTATE_NO_EXISTE, true)) {
                return [];
            }
            throw $e;
        }
    }

    /**
     * Esquemas de comun, con el mismo criterio que las migraciones multi-esquema.
     *
     * @return array<string, int> esquema => id_schema
     */
    private function esquemasComun(string $soloEsquema): array
    {
        $esquemas = [];
        foreach ($this->dbSchemaRepository->getDbSchemas(['_ordre' => 'schema']) as $dbSchema) {
            $schema = $dbSchema->getSchema();
            if (MigracionEjecucionUtiles::esEsquemaResto($schema)) {
                continue;
            }
            if (MigracionEjecucionUtiles::esEsquemaRegionStgrComun($schema)) {
                continue;
            }
            if ($dbSchema->getId() < 3000 || $dbSchema->getId() >= 4000) {
                continue;
            }
            if (str_ends_with($schema, 'v') || str_ends_with($schema, 'f')) {
                continue;
            }
            if ($soloEsquema !== '' && $schema !== $soloEsquema) {
                continue;
            }
            $esquemas[$schema] = $dbSchema->getId();
        }

        if ($soloEsquema !== '' && $esquemas === []) {
            throw new RuntimeException(sprintf('El esquema "%s" no está en el catálogo de comun', $soloEsquema));
        }

        return $esquemas;
    }

    private function conectar(string $claveEsquema): PDO
    {
        $oConfigDB = new ConfigDB('importar');
        $pdo = (new DBConnection($oConfigDB->getEsquema($claveEsquema)))->getPDO();
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }

    private static function comillas(string $identificador): string
    {
        return '"' . str_replace('"', '""', $identificador) . '"';
    }
}
