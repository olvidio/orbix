<?php

namespace src\notas\application;


use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;

/**
 * Construye la matriz "alumnos x asignaturas" que muestra
 * `asignaturas_pendientes`: por cada persona devuelve una fila con la
 * situacion (pendiente / cursada / aprobada) de cada asignatura del
 * bienio+cuadrienio.
 *
 * Sucesor de `apps/notas/model/TablaAlumnosAsignaturas.php` (eliminado).
 * Diferencias respecto del legacy:
 *
 *   - Ya no devuelve `web\\Lista`; devuelve arrays neutros
 *     (`['cabeceras' => [...], 'filas' => [...]]`). La construccion de la
 *     `Lista` se hace en el controller frontend, separando datos y UI.
 *   - Los dos metodos `getTablaCr`/`getTablaDl` (duplicados al 95 %) se
 *     unifican en un pipeline privado `construirTabla()`.
 *   - Las notas se cargan en un unico `getPersonaNotas(..., 'id_nom' => 'IN')`
 *     para todo el colectivo, en vez de dos consultas por persona (legacy
 *     hacia `2 * N` queries: una para `fin_bienio/cuadrienio` y otra para
 *     todas las notas de la persona).
 *   - Los magic numbers (`3000`, `9990`, `9998`, `9999`, `2000`, `1100`,
 *     `2500`) pasan a ser constantes nombradas.
 */
final class TablaAlumnosAsignaturas
{

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }
    /** Valor de celda cuando la asignatura esta pendiente. */
    public const CELDA_PENDIENTE = 1;
    /** Valor de celda cuando la asignatura esta cursada pero no superada. */
    public const CELDA_CURSADA = 2;
    /** Valor de celda cuando la asignatura esta aprobada / no procede. */
    public const CELDA_APROBADA = '';

    /** Rango de `id_nivel` del bienio+cuadrienio (asignaturas "regulares"). */
    private const ID_NIVEL_ASIG_DESDE = 1100;
    private const ID_NIVEL_ASIG_HASTA = 2500;
    /** Por debajo de `2000` se considera bienio; a partir de `2000`, cuadrienio. */
    private const ID_NIVEL_BIENIO_MAX = 2000;
    /** Umbral de `id_nivel` que marca las asignaturas "sintel" fin_bienio / fin_cuadrienio. */
    private const ID_NIVEL_MARCADOR = 9990;
    /** `id_asignatura` de la marca "bienio completado". */
    private const ID_ASIG_FIN_BIENIO = 9999;
    /** `id_asignatura` de la marca "cuadrienio completado". */
    private const ID_ASIG_FIN_CUADRIENIO = 9998;
    /** `id_asignatura > 3000` = asignatura opcional: tomamos el `id_nivel` de la propia nota. */
    private const ID_ASIG_OPCIONAL_UMBRAL = 3000;

    /**
     * Variante `ambito = rstgr`: filtra por una lista de delegaciones (`id_dl`)
     * de la region stgr y muestra el codigo de delegacion como columna "centro".
     *
     * @param array<int, int|string>  $aIdDl            ids de delegacion seleccionados.
     * @param array<int, string>      $aDelegacionesStgr mapa `id_dl => cod_dl`.
     * @return array{cabeceras: array<int, string>, filas: array<int, array<int, mixed>>}
     */
    public function paraRegionStgr(array $aIdDl, array $aDelegacionesStgr): array
    {
        $dlsTxt = [];
        foreach ($aIdDl as $idDl) {
            if (isset($aDelegacionesStgr[$idDl])) {
                $dlsTxt[] = "'" . $aDelegacionesStgr[$idDl] . "'";
            }
        }
        if (empty($dlsTxt)) {
            return ['cabeceras' => $this->cabeceras([]), 'filas' => []];
        }

        $wherePersonas = [
            'situacion' => 'A',
            'nivel_stgr' => 'b|c1|c2',
            'dl' => implode(',', $dlsTxt),
            '_ordre' => 'dl,nivel_stgr,apellido1,nom',
        ];
        $operadoresPersonas = [
            'nivel_stgr' => '~',
            'dl' => 'IN',
        ];

        return $this->construirTabla($wherePersonas, $operadoresPersonas, true);
    }

    /**
     * Variante `ambito = dl`: muestra toda la delegacion en situacion `A`,
     * con el centro de estudios como columna "centro".
     *
     * @return array{cabeceras: array<int, string>, filas: array<int, array<int, mixed>>}
     */
    public function paraDelegacion(): array
    {
        $wherePersonas = [
            'situacion' => 'A',
            'nivel_stgr' => NivelStgrId::B . ',' . NivelStgrId::C1 . ',' . NivelStgrId::C2 . ',' . NivelStgrId::BC,
            '_ordre' => 'nivel_stgr,apellido1,nom',
        ];
        $operadoresPersonas = ['nivel_stgr' => 'IN'];

        return $this->construirTabla($wherePersonas, $operadoresPersonas, false);
    }

    /**
     * @param bool $usarDlComoCentro cuando `true`, la columna 3 (centro) contiene
     *   el codigo de delegacion (`getDl()`); cuando `false`, el centro de estudios
     *   (`getCentro_o_dl()`).
     * @param array<string, mixed> $wherePersonas
     * @param array<string, string> $operadoresPersonas
     * @return array{cabeceras: array<int, string>, filas: array<int, array<int, mixed>>}
     */
    private function construirTabla(array $wherePersonas, array $operadoresPersonas, bool $usarDlComoCentro): array
    {
        $asignaturaRepo = $this->asignaturaRepository;
        $personaRepo = $this->personaDlRepository;
        $personaNotaRepo = $this->personaNotaRepository;

        $asignaturas = $asignaturaRepo->getAsignaturas([
            'active' => 't',
            'id_nivel' => self::ID_NIVEL_ASIG_DESDE . ',' . self::ID_NIVEL_ASIG_HASTA,
            '_ordre' => 'id_nivel',
        ], ['id_nivel' => 'BETWEEN']);

        // Mapa `id_asignatura => id_nivel` para todas las asignaturas (incluidas
        // inactivas). Necesario para resolver el nivel de una nota cuando la
        // asignatura no es opcional (`id_asignatura <= 3000`).
        $mapAsigNivel = [];
        $todas = $asignaturaRepo->getAsignaturas(['_ordre' => 'id_asignatura']);
        foreach ($todas as $oAsig) {
                $mapAsigNivel[(int)$oAsig->getId_asignatura()] = (int)$oAsig->getId_nivel();
        }

        $cabeceras = $this->cabeceras($asignaturas);

        $cPersonas = $personaRepo->getPersonas($wherePersonas, $operadoresPersonas);
        if (empty($cPersonas)) {
            return ['cabeceras' => $cabeceras, 'filas' => []];
        }

        // Batch de `PersonaNota` para todo el colectivo (un unico query con IN).
        $aIdNoms = [];
        foreach ($cPersonas as $oPersona) {
            $aIdNoms[] = (int)$oPersona->getId_nom();
        }
        $notasPorPersona = $this->cargarNotasPorPersona($personaNotaRepo, $aIdNoms);

        $superadas = NotaSituacion::getArraySuperadas();
        $filas = [];
        $pos = 0;
        foreach ($cPersonas as $oPersona) {
            $pos++;
            $idNom = (int)$oPersona->getId_nom();
            $filas[$pos] = $this->construirFila(
                $oPersona,
                array_values($notasPorPersona[$idNom] ?? []),
                $asignaturas,
                $mapAsigNivel,
                $superadas,
                $usarDlComoCentro,
            );
        }

        return ['cabeceras' => $cabeceras, 'filas' => $filas];
    }

    /**
     * @param list<\src\asignaturas\domain\entity\Asignatura> $asignaturas
     * @return array<int, string>
     */
    private function cabeceras(array $asignaturas): array
    {
        $cabeceras = [
            0 => _("n/a"),
            1 => _("nivel stgr"),
            2 => _("centro"),
            3 => _("apellidos, nombre"),
        ];
        $col = 3;
        foreach ($asignaturas as $oAsig) {
            $col++;
            $creditos = $oAsig->getCreditos() ?? '';
            $cabeceras[$col] = $oAsig->getNombre_corto() . ' (' . $creditos . ')';
        }
        return $cabeceras;
    }

    /**
     * @param array<int, int> $aIdNoms
     * @return array<int, PersonaNota[]> indexado por `id_nom`.
     */
    private function cargarNotasPorPersona(PersonaNotaRepositoryInterface $repo, array $aIdNoms): array
    {
        if (empty($aIdNoms)) {
            return [];
        }
        $cNotas = $repo->getPersonaNotas(
            ['id_nom' => implode(',', $aIdNoms)],
            ['id_nom' => 'IN'],
        );
        $agrupado = [];
        foreach ($cNotas as $oNota) {
            $agrupado[(int)$oNota->getId_nom()][] = $oNota;
        }
        return $agrupado;
    }

    /**
     * @param \src\personas\domain\entity\PersonaDl $oPersona
     * @param list<PersonaNota> $notasPersona
     * @param list<\src\asignaturas\domain\entity\Asignatura> $asignaturas
     * @param array<int, int> $mapAsigNivel
     * @param list<int> $superadas
     * @return array<int, mixed>
     */
    private function construirFila(
        \src\personas\domain\entity\PersonaDl $oPersona,
        array $notasPersona,
        array $asignaturas,
        array $mapAsigNivel,
        array $superadas,
        bool $usarDlComoCentro,
    ): array
    {
        $fila = [
            1 => $oPersona->getId_tabla(),
            2 => $oPersona->getNivel_stgr(),
            3 => $usarDlComoCentro ? $oPersona->getDl() : $oPersona->getCentro_o_dl(),
            4 => $oPersona->getPrefApellidosNombre(),
        ];

        [$finBienio, $finCuadrienio] = $this->detectarTramosCompletados($notasPersona);
        $aprobadasPorNivel = $this->mapearAprobadasPorNivel($notasPersona, $mapAsigNivel, $superadas);

        $col = 4;
        foreach ($asignaturas as $oAsig) {
            $col++;
            $idNivel = (int)$oAsig->getId_nivel();
            if (array_key_exists($idNivel, $aprobadasPorNivel)) {
                $valor = $aprobadasPorNivel[$idNivel];
            } else {
                $valor = self::CELDA_PENDIENTE;
            }
            // Si la persona ya ha completado el bienio o el cuadrienio, las
            // asignaturas pendientes del tramo se blanquean (no se consideran
            // "pendientes" a efectos del cuadro).
            if ($finBienio && $idNivel < self::ID_NIVEL_BIENIO_MAX) {
                $valor = self::CELDA_APROBADA;
            }
            if ($finCuadrienio) {
                $valor = self::CELDA_APROBADA;
            }
            $fila[$col] = $valor;
        }

        return $fila;
    }

    /**
     * @param PersonaNota[] $notasPersona
     * @return array{0: bool, 1: bool} `[finBienio, finCuadrienio]`.
     */
    private function detectarTramosCompletados(array $notasPersona): array
    {
        $finBienio = false;
        $finCuadrienio = false;
        foreach ($notasPersona as $oNota) {
            if ((int)$oNota->getIdNivelVo()->value() <= self::ID_NIVEL_MARCADOR) {
                continue;
            }
            $idAsig = (int)$oNota->getId_asignatura();
            if ($idAsig === self::ID_ASIG_FIN_BIENIO) {
                $finBienio = true;
            } elseif ($idAsig === self::ID_ASIG_FIN_CUADRIENIO) {
                $finCuadrienio = true;
            }
        }
        return [$finBienio, $finCuadrienio];
    }

    /**
     * Convierte la lista de notas de la persona en un mapa
     * `id_nivel => valor_celda` (`CELDA_CURSADA` o `CELDA_APROBADA`).
     *
     * @param PersonaNota[]   $notasPersona
     * @param array<int, int> $mapAsigNivel
     * @param array<int, int> $superadas
     * @return array<int, mixed>
     */
    private function mapearAprobadasPorNivel(array $notasPersona, array $mapAsigNivel, array $superadas): array
    {
        $aprobadasPorNivel = [];
        foreach ($notasPersona as $oNota) {
            $idAsig = (int)$oNota->getId_asignatura();
            $idSit = (int)$oNota->getId_situacion();
            $idNivelAsig = ($idAsig > self::ID_ASIG_OPCIONAL_UMBRAL)
                ? (int)$oNota->getIdNivelVo()->value()
                : ($mapAsigNivel[$idAsig] ?? null);
            if ($idNivelAsig === null) {
                continue;
            }
            if ($idSit === NotaSituacion::CURSADA) {
                $aprobadasPorNivel[$idNivelAsig] = self::CELDA_CURSADA;
            } elseif (in_array($idSit, $superadas, true)) {
                $aprobadasPorNivel[$idNivelAsig] = self::CELDA_APROBADA;
            }
        }
        return $aprobadasPorNivel;
    }
}
