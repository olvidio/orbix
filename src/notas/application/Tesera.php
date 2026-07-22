<?php

namespace src\notas\application;


use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Servicio de "tessera studiorum": dado un `id_nom` calcula las asignaturas
 * posibles, las aprobadas y compone el dataset que consumen las tres pantallas:
 *
 *   - Tessera vista HTML (`tessera_ver.php`): dataset vía `TesseraVerData`
 *     (`/src/notas/tessera_ver_data`), que delega en `datosParaVistaTesera`.
 *   - Tessera imprimible / PDF: `tessera_imprimir.php` / `tessera_imprimir_mpdf.php`
 *     cargan vía `TesseraImprimirData` (`/src/notas/tessera_imprimir_data`).
 *
 * Sucesor de `apps/notas/model/Tesera.php` (eliminado).
 * Mejoras respecto al legacy:
 *
 *   - Ya no renderiza vistas (`ViewNewPhtml`): `datosParaVistaTesera()`
 *     devuelve un array neutro y la PHTML monta el HTML (separacion
 *     datos / UI, `refactor.md`).
 *   - Magic numbers encapsulados como constantes nombradas
 *     (`ID_NIVEL_ASIG_DESDE/HASTA`, `ID_ASIG_OPCIONAL_UMBRAL`,
 *     `ID_ASIG_OPCIONAL_MAX`, `PLAN_NUEVO`, `PLAN_VIEJO`).
 *   - La merge de `cAsignaturas` + `aAprobadas` esta saneada para
 *     no acceder fuera de rango de `cAsignaturas` (bug latente del
 *     modelo legacy cuando la ultima asignatura era pendiente).
 *   - Los metodos `getCurso/getTitulo/getVariasTesera` (privados o
 *     sin consumidor) desaparecen: `getCurso` se reemplaza por
 *     `cursoActual()` publico tipado; `getTitulo` pasa a la vista;
 *     `getVariasTesera` era una funcion vacia (codigo muerto).
 */
final class Tesera
{

    public function __construct(
        private readonly ExpedienteNotasPersona $expedienteNotasPersona,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }
    /** Rango de `id_nivel` de asignaturas de bienio+cuadrienio. */
    private const ID_NIVEL_ASIG_DESDE = 1100;
    private const ID_NIVEL_ASIG_HASTA = 2500;
    /** `id_asignatura > 3000` = asignatura opcional (se usa `id_nivel` de la nota). */
    private const ID_ASIG_OPCIONAL_UMBRAL = 3000;
    /** Rango de `id_asignatura` que se consideran "opcionales" visibles en la tessera. */
    private const ID_ASIG_OPCIONAL_MAX = 9000;

    /** Plan de estudios vigente (por defecto). */
    public const PLAN_NUEVO = PlanEstudios::PLAN_2026;
    /** Plan de estudios anterior (anterior a 2026-03-30). */
    public const PLAN_VIEJO = PlanEstudios::PLAN_1997;

    /**
     * Devuelve el curso academico actual segun la configuracion (`diaIniStgr`,
     * `mesIniStgr`, `diaFinStgr`, `mesFinStgr`).
     *
     * @return array{inicio: DateTimeLocal, fin: DateTimeLocal, texto: string}
     */
    public function cursoActual(): array
    {
        $config = $_SESSION['oConfig'] ?? null;
        if (!$config instanceof ConfigSnapshot) {
            throw new \RuntimeException('Configuracion de sesion no disponible');
        }
        $ini_d = $config->getDiaIniStgr();
        $ini_m = $config->getMesIniStgr();
        $fin_d = $config->getDiaFinStgr();
        $fin_m = $config->getMesFinStgr();

        $any = (int)date('Y');
        $mes = (int)date('m');

        if ($mes > (int)$fin_m) {
            $any2 = $any - 1;
            $texto = "$any2-$any";
        } else {
            $any2 = $any - 2;
            $any--;
            $texto = "$any2-$any";
        }

        return [
            'inicio' => new DateTimeLocal("$any2-$ini_m-$ini_d"),
            'fin' => new DateTimeLocal("$any-$fin_m-$fin_d"),
            'texto' => $texto,
        ];
    }

    /**
     * Determina el plan de estudios aplicable a una persona via
     * {@see PlanEstudiosDePersona::resolve()}.
     *
     * @return int Año del plan ({@see PlanEstudios::PLAN_1997} o {@see PlanEstudios::PLAN_2026})
     */
    public function getPlan(int $idNom): int
    {
        return $this->planEstudiosDePersona->resolve($idNom);
    }

    /**
     * Asignaturas activas del bienio+cuadrienio para el plan indicado,
     * ordenadas por `id_nivel`.
     *
     * @param int $plan Año del plan ({@see PlanEstudios::PLAN_1997} o {@see PlanEstudios::PLAN_2026})
     * @return Asignatura[]
     */
    public function getAsignaturasPosibles(int $plan = self::PLAN_NUEVO): array
    {
        [$aWhere, $aOperador] = PlanEstudiosFilter::apply($plan, [
            'active' => 't',
            'id_nivel' => self::ID_NIVEL_ASIG_DESDE . ',' . self::ID_NIVEL_ASIG_HASTA,
            '_ordre' => 'id_nivel',
        ], ['id_nivel' => 'BETWEEN']);

        return $this->asignaturaRepository->getAsignaturas($aWhere, $aOperador);
    }

    /**
     * Asignaturas que ha cursado / aprobado la persona, indexadas y ordenadas
     * por `id_nivel_asig`. Cada entrada tiene las claves:
     *   `id_nivel_asig`, `id_nivel`, `id_asignatura`, `nombre_asignatura`,
     *   `nombre_corto`, `fecha` (`DateTimeLocal`), `id_situacion`,
     *   `bAprobada` (`'t'|'f'`), `nota` (string), `acta` (string|null).
     *
     * @param int $plan Año del plan ({@see PlanEstudios::PLAN_1997} o {@see PlanEstudios::PLAN_2026})
     * @return array<int, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, nombre_corto: string, fecha: \src\shared\domain\value_objects\DateTimeLocal|null, id_situacion: int, bAprobada: bool|string, nota: string|null, acta: string|null}>
     */
    public function getAsignaturasAprobadas(int $idNom, int $plan = self::PLAN_NUEVO): array
    {
        $asignaturaRepo = $this->asignaturaRepository;

        // Expediente agregado (publicv.e_notas: e_notas_dl + otra_region; acta > certificado).
        $cNotas = $this->expedienteNotasPersona->getNotas($idNom);

        $aAprobadas = [];
        foreach ($cNotas as $oNota) {
            $idNivel = (int)$oNota->getIdNivelVo()->value();
            if ($idNivel < self::ID_NIVEL_ASIG_DESDE || $idNivel > self::ID_NIVEL_ASIG_HASTA) {
                continue;
            }

            $idAsig = (int)$oNota->getId_asignatura();

            $oAsig = $asignaturaRepo->findById($idAsig, $plan);
            if ($oAsig === null) {
                continue;
            }

            if ($idAsig > self::ID_ASIG_OPCIONAL_UMBRAL) {
                $idNivelAsig = $idNivel;
                $oAsignaturaOpcionalGenerica = $asignaturaRepo->findById($idNivel, $plan);
                if ($oAsignaturaOpcionalGenerica === null || !$oAsignaturaOpcionalGenerica->isActive()) {
                    continue;
                }
            } else {
                if (!$oAsig->isActive()) {
                    continue;
                }
                $idNivelAsig = (int) $oAsig->getId_nivel();
            }

            $aAprobadas[$idNivelAsig] = [
                'id_nivel_asig' => $idNivelAsig,
                'id_nivel' => $idNivel,
                'id_asignatura' => $idAsig,
                'nombre_asignatura' => $oAsig->getNombreAsignaturaVo()->value(),
                'nombre_corto' => (string) ($oAsig->getNombre_corto() ?? ''),
                'fecha' => $oNota->getF_acta(),
                'id_situacion' => (int)$oNota->getId_situacion(),
                'bAprobada' => $oNota->isAprobada(),
                'nota' => $oNota->getNota_txt(),
                'acta' => $oNota->getActaVo()?->value(),
            ];
        }
        ksort($aAprobadas);

        return $aAprobadas;
    }

    /**
     * Dataset completo para `frontend/notas/view/tesera_ver.phtml`:
     * cabecera (`ap_nom`, `centro`), filas de la tabla y estadisticas
     * (`numasig`, `numcred`, `numasig_year`, `numcred_year`, totales).
     *
     * Las filas de `tabla` son arrays neutros con:
     *   `id_nivel`, `titulo_seccion` (opcional, cabecera de bloque curricular),
     *   `asignatura` (nombre_corto, con el nombre de la opcional si procede), `nota`
     *   (`-1` si pendiente), `fecha` (string `'d-m-Y'` o `''`), `bAprobada`
     *   (`'t'|'f'`). Solo entran asignaturas del plan vigente.
     *
     * @return array<string, mixed>
     */
    public function datosParaVistaTesera(int $idNom): array
    {
        $oPersona = Persona::findPersonaEnGlobal($idNom);
        if ($oPersona === null) {
            throw new \RuntimeException(sprintf('Persona no encontrada: %d', $idNom));
        }
        $ap_nom = $oPersona->getPrefApellidosNombre();
        $centro = $oPersona->getCentro_o_dl();

        $plan = $this->getPlan($idNom);
        $cAsignaturas = $this->getAsignaturasPosibles($plan);
        $aAprobadas = $this->getAsignaturasAprobadas($idNom, $plan);
        $curso = $this->cursoActual();

        $numAsigTotal = count($cAsignaturas);
        $numCreditosTotal = 0.0;
        foreach ($cAsignaturas as $oAsig) {
            $numCreditosTotal += (float) $oAsig->getCreditos();
        }

        $aprobadosList = array_values($aAprobadas);
        $numAprob = count($aprobadosList);
        $planNiveles = [];
        foreach ($cAsignaturas as $oAsigPlan) {
            $planNiveles[(int) $oAsigPlan->getId_nivel()] = true;
        }

        $j = 0;
        $tabla = [];
        $i = 0;
        $seccionActual = null;
        $numasig = 0;
        $numcred = 0.0;
        $numasigYear = 0;
        $numcredYear = 0.0;

        foreach ($cAsignaturas as $oAsig) {
            while (
                $j < $numAprob
                && !isset($planNiveles[(int) $aprobadosList[$j]['id_nivel_asig']])
            ) {
                $j++;
            }

            $idNivelPlan = (int) $oAsig->getId_nivel();
            if ($j < $numAprob && (int) $aprobadosList[$j]['id_nivel_asig'] === $idNivelPlan) {
                $row = $aprobadosList[$j++];
                $i++;
                $tabla[$i] = $this->filaAprobada($oAsig, $row);
                $this->anotarTituloSeccion($tabla, $i, $idNivelPlan, $seccionActual);
                $this->acumularEstadisticasAprobada(
                    $row,
                    $curso,
                    $numasig,
                    $numcred,
                    $numasigYear,
                    $numcredYear,
                    $oAsig,
                );
            } else {
                $i++;
                $tabla[$i] = $this->filaPendiente($oAsig);
                $this->anotarTituloSeccion($tabla, $i, $idNivelPlan, $seccionActual);
            }
        }

        return [
            'ap_nom' => $ap_nom,
            'centro' => $centro,
            'tabla' => $tabla,
            'numasig' => $numasig,
            'num_asig_total' => $numAsigTotal,
            'numasig_year' => $numasigYear,
            'numcred' => $numcred,
            'num_creditos_total' => $numCreditosTotal,
            'curso_txt' => $curso['texto'],
            'numcred_year' => $numcredYear,
        ];
    }

    private static function seccionTituloDeIdNivel(int $idNivel): ?string
    {
        return match (true) {
            $idNivel >= 1100 && $idNivel < 1200 => 'filosofia_anno_i',
            $idNivel >= 1200 && $idNivel < 2100 => 'filosofia_anno_ii',
            $idNivel >= 2100 && $idNivel < 2200 => 'teologia_anno_i',
            $idNivel >= 2200 && $idNivel < 2300 => 'teologia_anno_ii',
            $idNivel >= 2300 && $idNivel < 2400 => 'teologia_anno_iii',
            $idNivel >= 2400 && $idNivel < 2500 => 'teologia_anno_iv',
            default => null,
        };
    }

    /**
     * @param array<int, array<string, mixed>> $tabla
     */
    private function anotarTituloSeccion(array &$tabla, int $i, int $idNivel, ?string &$seccionActual): void
    {
        $seccion = self::seccionTituloDeIdNivel($idNivel);
        if ($seccion === null || $seccion === $seccionActual) {
            return;
        }
        $tabla[$i]['titulo_seccion'] = $seccion;
        $seccionActual = $seccion;
    }

    /**
     * @param array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, nombre_corto: string, fecha: DateTimeLocal|null, bAprobada: bool|string, nota: string|null} $row
     * @param array{inicio: DateTimeLocal, fin: DateTimeLocal, texto?: string} $curso
     */
    private function acumularEstadisticasAprobada(
        array $row,
        array $curso,
        int &$numasig,
        float &$numcred,
        int &$numasigYear,
        float &$numcredYear,
        Asignatura $oAsig,
    ): void {
        if (!\src\shared\domain\helpers\FuncTablasSupport::isTrue($row['bAprobada'])) {
            return;
        }

        $creditos = (float) ($oAsig->getCreditos() ?? 0);
        $numasig++;
        $numcred += $creditos;
        $oFActa = $row['fecha'];
        if ($oFActa !== null && $curso['inicio'] <= $oFActa && $oFActa <= $curso['fin']) {
            $numasigYear++;
            $numcredYear += $creditos;
        }
    }

    /**
     * @return array{id_nivel: int, asignatura: string, nota: int, fecha: string, bAprobada: string}
     */
    private function filaPendiente(Asignatura $oAsig): array
    {
        return [
            'id_nivel' => (int)$oAsig->getId_nivel(),
            'asignatura' => (string)$oAsig->getNombre_corto(),
            'nota' => -1,
            'fecha' => '',
            'bAprobada' => 'f',
        ];
    }

    /**
     * @param array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_corto: string, fecha: \src\shared\domain\value_objects\DateTimeLocal|null, nota: string|null, bAprobada: bool|string} $row
     * @return array{id_nivel: int, asignatura: string, nota: string, fecha: string, bAprobada: string}
     */
    private function filaAprobada(Asignatura $oAsig, array $row): array
    {
        $idAsig = (int) $row['id_asignatura'];
        $nombreCortoRow = (string) $row['nombre_corto'];
        if ($idAsig > self::ID_ASIG_OPCIONAL_UMBRAL && $idAsig < self::ID_ASIG_OPCIONAL_MAX) {
            $asignatura = $oAsig->getNombre_corto() . '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $nombreCortoRow;
        } else {
            $asignatura = (string)$oAsig->getNombre_corto();
        }
        return [
            'id_nivel' => (int)$oAsig->getId_nivel(),
            'asignatura' => $asignatura,
            'nota' => (string) ($row['nota'] ?? ''),
            'fecha' => $row['fecha']?->getFromLocal() ?? '',
            'bAprobada' => (string) $row['bAprobada'],
        ];
    }
}
