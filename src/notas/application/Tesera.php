<?php

namespace src\notas\application;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;
use function core\is_true;

/**
 * Servicio de "tessera studiorum": dado un `id_nom` calcula las asignaturas
 * posibles, las aprobadas y compone el dataset que consumen las tres pantallas:
 *
 *   - `frontend/notas/controller/tessera_ver.php`        (vista HTML).
 *   - `frontend/notas/controller/tessera_imprimir.php`   (tessera imprimible).
 *   - `frontend/notas/controller/tessera_imprimir_mpdf.php` (PDF).
 *
 * Sucesor de `apps/notas/model/Tesera.php` (eliminado).
 * Mejoras respecto al legacy:
 *
 *   - Ya no renderiza vistas (`ViewNewPhtml`): `datosParaVistaTesera()`
 *     devuelve un array neutro y la PHTML monta el HTML (separacion
 *     datos / UI, `refactor.md`).
 *   - Magic numbers encapsulados como constantes nombradas
 *     (`ID_NIVEL_ASIG_DESDE/HASTA`, `ID_NIVEL_MAX_CUADRIENIO`,
 *     `ID_ASIG_OPCIONAL_UMBRAL`, `ID_ASIG_OPCIONAL_MAX`,
 *     `ID_ASIG_FIN_CUADRIENIO`, `PLAN_NUEVO`, `PLAN_VIEJO`,
 *     `ID_NIVEL_PLAN97_DESAPARECIDO`, `ID_NIVEL_PLAN97_NUEVOS`,
 *     `FECHA_LIMITE_PLAN97`).
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
    /** Rango de `id_nivel` de asignaturas de bienio+cuadrienio. */
    private const ID_NIVEL_ASIG_DESDE = 1100;
    private const ID_NIVEL_ASIG_HASTA = 2500;
    /**
     * Limite superior de `id_nivel` usado en el merge. Las notas con
     * `id_nivel >= 2434` no se cuentan como "pendientes" al intercalar las
     * aprobadas (marcadores fin-bienio, fin-cuadrienio, ...).
     */
    private const ID_NIVEL_MAX_CUADRIENIO = 2434;
    /** `id_asignatura > 3000` = asignatura opcional (se usa `id_nivel` de la nota). */
    private const ID_ASIG_OPCIONAL_UMBRAL = 3000;
    /** Rango de `id_asignatura` que se consideran "opcionales" visibles en la tessera. */
    private const ID_ASIG_OPCIONAL_MAX = 9000;
    /** `id_asignatura` de la marca "cuadrienio completado". */
    private const ID_ASIG_FIN_CUADRIENIO = 9998;

    /** Plan de estudios "nuevo" (por defecto). */
    public const PLAN_NUEVO = 26;
    /** Plan de estudios "viejo" (anterior a 2026-03-30). */
    public const PLAN_VIEJO = 97;

    /** `id_nivel` del plan viejo que desaparece en el nuevo. */
    private const ID_NIVEL_PLAN97_DESAPARECIDO = 2114;
    /** `id_nivel` que reemplazan al anterior en el plan viejo. */
    private const ID_NIVEL_PLAN97_NUEVOS = '2112,2113';
    /**
     * Fecha limite que separa los planes: las personas con marca
     * `cuadrienio completado` anterior a esta fecha pertenecen al plan viejo.
     */
    private const FECHA_LIMITE_PLAN97 = '2026-03-30';

    /**
     * Devuelve el curso academico actual segun la configuracion (`diaIniStgr`,
     * `mesIniStgr`, `diaFinStgr`, `mesFinStgr`).
     *
     * @return array{inicio: DateTimeLocal, fin: DateTimeLocal, texto: string}
     */
    public function cursoActual(): array
    {
        $config = $_SESSION['oConfig'];
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
     * Determina el plan de estudios aplicable a una persona: las personas
     * con "cuadrienio completado" anterior a {@see FECHA_LIMITE_PLAN97}
     * se consideran del plan viejo ({@see PLAN_VIEJO}); resto, plan nuevo.
     */
    public function getPlan(int $idNom): int
    {
        $repo = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $cNotas = $repo->getPersonaNotas([
            'id_nom' => $idNom,
            'id_asignatura' => self::ID_ASIG_FIN_CUADRIENIO,
        ]);
        if (count($cNotas) === 0) {
            return self::PLAN_NUEVO;
        }
        $oFActa = $cNotas[0]->getF_acta();
        $oLimite = new DateTimeLocal(self::FECHA_LIMITE_PLAN97);
        return ($oFActa < $oLimite) ? self::PLAN_VIEJO : self::PLAN_NUEVO;
    }

    /**
     * Asignaturas activas del bienio+cuadrienio, ordenadas por `id_nivel`.
     * Cuando el plan aplicable es el viejo, se sustituye `id_nivel = 2114`
     * por las asignaturas `2112, 2113` (desaparecidas en el plan nuevo).
     *
     * @return Asignatura[]
     */
    public function getAsignaturasPosibles(int $plan = self::PLAN_NUEVO): array
    {
        $repo = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $cAsignaturas = $repo->getAsignaturas([
            'active' => 't',
            'id_nivel' => self::ID_NIVEL_ASIG_DESDE . ',' . self::ID_NIVEL_ASIG_HASTA,
            '_ordre' => 'id_nivel',
        ], ['id_nivel' => 'BETWEEN']);
        $cAsignaturas = is_array($cAsignaturas) ? $cAsignaturas : [];

        if ($plan !== self::PLAN_VIEJO) {
            return $cAsignaturas;
        }

        // Plan viejo: se reintroducen 2112/2113 en lugar de 2114.
        $cPlan97 = $repo->getAsignaturas([
            'id_nivel' => self::ID_NIVEL_PLAN97_NUEVOS,
            '_ordre' => 'id_nivel',
        ], ['id_nivel' => 'IN']);
        $cPlan97 = is_array($cPlan97) ? $cPlan97 : [];

        $resultado = [];
        foreach ($cAsignaturas as $oAsig) {
            if ((int)$oAsig->getId_nivel() === self::ID_NIVEL_PLAN97_DESAPARECIDO) {
                $resultado = array_merge($resultado, $cPlan97);
                continue;
            }
            $resultado[] = $oAsig;
        }
        return $resultado;
    }

    /**
     * Asignaturas que ha cursado / aprobado la persona, indexadas y ordenadas
     * por `id_nivel_asig`. Cada entrada tiene las claves:
     *   `id_nivel_asig`, `id_nivel`, `id_asignatura`, `nombre_asignatura`,
     *   `nombre_corto`, `fecha` (`DateTimeLocal`), `id_situacion`,
     *   `bAprobada` (`'t'|'f'`), `nota` (string), `acta` (string|null).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAsignaturasAprobadas(int $idNom, int $plan = self::PLAN_NUEVO): array
    {
        $personaNotaRepo = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $asignaturaRepo = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);

        $cNotas = $personaNotaRepo->getPersonaNotas([
            'id_nom' => $idNom,
            'id_nivel' => self::ID_NIVEL_ASIG_DESDE . ',' . self::ID_NIVEL_ASIG_HASTA,
        ], ['id_nivel' => 'BETWEEN']);

        $aAprobadas = [];
        foreach ($cNotas as $oNota) {
            $idAsig = (int)$oNota->getId_asignatura();
            $idNivel = (int)$oNota->getIdNivelVo()->value();

            $oAsig = $asignaturaRepo->findById($idAsig);
            if ($oAsig === null) {
                throw new \RuntimeException(sprintf(_("No se ha encontrado la asignatura con id: %s"), $idAsig));
            }

            if ($idAsig > self::ID_ASIG_OPCIONAL_UMBRAL) {
                $idNivelAsig = $idNivel;
            } else {
                if ($plan === self::PLAN_VIEJO) {
                    if ($idNivel === self::ID_NIVEL_PLAN97_DESAPARECIDO) {
                        continue;
                    }
                } elseif (!$oAsig->isActive()) {
                    continue;
                }
                $idNivelAsig = (int)$oAsig->getId_nivel();
            }

            $aAprobadas[$idNivelAsig] = [
                'id_nivel_asig' => $idNivelAsig,
                'id_nivel' => $idNivel,
                'id_asignatura' => $idAsig,
                'nombre_asignatura' => $oAsig->getNombreAsignaturaVo()->value(),
                'nombre_corto' => $oAsig->getNombre_corto(),
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
     *   `id_nivel` (para que la vista decida la cabecera), `asignatura`
     *   (nombre_corto, con el nombre de la opcional si procede), `nota`
     *   (`-1` si pendiente), `fecha` (string `'d-m-Y'` o `''`), `bAprobada`
     *   (`'t'|'f'`).
     *
     * @return array<string, mixed>
     */
    public function datosParaVistaTesera(int $idNom): array
    {
        $oPersona = Persona::findPersonaEnGlobal($idNom);
        $ap_nom = $oPersona->getPrefApellidosNombre();
        $centro = $oPersona->getCentro_o_dl();

        $plan = $this->getPlan($idNom);
        $cAsignaturas = $this->getAsignaturasPosibles($plan);
        $aAprobadas = $this->getAsignaturasAprobadas($idNom, $plan);
        $curso = $this->cursoActual();

        $numAsigTotal = count($cAsignaturas);
        $numCreditosTotal = 0.0;
        $numasig = 0;
        $numcred = 0.0;
        $numasigYear = 0;
        $numcredYear = 0.0;

        reset($aAprobadas);
        $tabla = [];
        $i = 0;
        $a = 0;
        while ($a < $numAsigTotal) {
            $oAsig = $cAsignaturas[$a++];
            $numCreditosTotal += (float)$oAsig->getCreditos();
            $row = current($aAprobadas);
            next($aAprobadas);

            if ($row === false) {
                $i++;
                $tabla[$i] = $this->filaPendiente($oAsig);
                continue;
            }

            while (
                (int)$oAsig->getId_nivel() < (int)$row['id_nivel_asig']
                && (int)$row['id_nivel'] < self::ID_NIVEL_MAX_CUADRIENIO
                && $a < $numAsigTotal
            ) {
                $i++;
                $tabla[$i] = $this->filaPendiente($oAsig);
                $oAsig = $cAsignaturas[$a++];
                $numCreditosTotal += (float)$oAsig->getCreditos();
            }

            if ((int)$oAsig->getId_nivel() === (int)$row['id_nivel_asig']) {
                $i++;
                $tabla[$i] = $this->filaAprobada($oAsig, $row);

                if (is_true($row['bAprobada'])) {
                    $numasig++;
                    $numcred += (float)$oAsig->getCreditos();
                    $oFActa = $row['fecha'];
                    if ($curso['inicio'] <= $oFActa && $oFActa <= $curso['fin']) {
                        $numasigYear++;
                        $numcredYear += (float)$oAsig->getCreditos();
                    }
                }
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

    /**
     * @return array{id_nivel: int, asignatura: string, nota: int, fecha: string|int, bAprobada: string}
     */
    private function filaPendiente(Asignatura $oAsig): array
    {
        return [
            'id_nivel' => (int)$oAsig->getId_nivel(),
            'asignatura' => (string)$oAsig->getNombre_corto(),
            'nota' => -1,
            'fecha' => -1,
            'bAprobada' => 'f',
        ];
    }

    /**
     * @param array<string, mixed> $row fila de `getAsignaturasAprobadas()`.
     * @return array{id_nivel: int, asignatura: string, nota: mixed, fecha: string, bAprobada: string}
     */
    private function filaAprobada(Asignatura $oAsig, array $row): array
    {
        $idAsig = (int)$row['id_asignatura'];
        if ($idAsig > self::ID_ASIG_OPCIONAL_UMBRAL && $idAsig < self::ID_ASIG_OPCIONAL_MAX) {
            $asignatura = $oAsig->getNombre_corto() . '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $row['nombre_corto'];
        } else {
            $asignatura = (string)$oAsig->getNombre_corto();
        }
        return [
            'id_nivel' => (int)$oAsig->getId_nivel(),
            'asignatura' => $asignatura,
            'nota' => $row['nota'],
            'fecha' => $row['fecha']->getFromLocal(),
            'bAprobada' => $row['bAprobada'],
        ];
    }
}
