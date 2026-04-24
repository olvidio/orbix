<?php

namespace src\actividadestudios\application;

use core\ConfigGlobal;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\entity\Persona;

/**
 * Matricula automaticamente a una o varias personas en las asignaturas
 * correspondientes a su plan de estudios vigente del curso actual.
 *
 * - Si se recibe `id_pau`/`sel`, trabaja sobre una persona concreta (y opcionalmente
 *   una actividad via `id_activ`).
 * - Si no, recorre a todas las personas en situacion `A` (activos) de la dl.
 *
 * Para cada persona:
 * 1. Determina la actividad de estudios activa (`ca-n`, `cv-agd`).
 * 2. Borra las matriculas previas (si el plan no esta confirmado).
 * 3. Recalcula las asignaturas matriculables, respetando las aprobadas y los
 *    topes de las opcionales por bienio/cuadrienio.
 *
 * Sustituye a `apps/actividadestudios/controller/matricular.php`.
 */
final class MatriculaAutomatica
{
    public static function execute(array $input): string
    {
        $msg = '';

        $a_sel = (array) ($input['sel'] ?? []);
        $Qid_activ = 0;
        if (!empty($a_sel)) {
            $Qid_nom = (int) strtok((string) $a_sel[0], '#');
        } else {
            $Qid_nom = (int) ($input['id_pau'] ?? 0);
            $Qid_activ = (int) ($input['id_activ'] ?? 0);
        }

        $mes = (int) date('m');
        $fin_m = $_SESSION['oConfig']->getMesFinStgr();
        $any = ($mes > $fin_m) ? (int) date('Y') + 1 : (int) date('Y');
        $inicurs_ca = \core\curso_est('inicio', $any)->format('Y-m-d');
        $fincurs_ca = \core\curso_est('fin', $any)->format('Y-m-d');

        $aWhere = [];
        $aOperador = [];
        if (!empty($Qid_nom)) {
            $aWhere['id_nom'] = $Qid_nom;
            $aWhere['nivel_stgr'] = NivelStgrId::R;
            $aOperador['nivel_stgr'] = '!=';

            $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
            if ($oPersona === null) {
                return sprintf(_('No se ha encontrado a la persona con id: %s'), $Qid_nom);
            }
            $classname = str_replace('personas\\model\\entity\\', '', get_class($oPersona));

            if ($classname === 'PersonaEx') {
                $PersonaExRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
                $cAlumnos = $PersonaExRepository->getPersonas($aWhere, $aOperador);
            } else {
                $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
                $cAlumnos = $PersonaDlRepository->getPersonas($aWhere, $aOperador);
            }
            if (empty($cAlumnos)) {
                $msg = _('está de repaso');
            }
        } else {
            $aWhere['situacion'] = 'A';
            $aWhere['nivel_stgr'] = NivelStgrId::R;
            $aOperador['nivel_stgr'] = '!=';
            $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
            $cAlumnos = $PersonaDlRepository->getPersonas($aWhere, $aOperador);
        }

        $aWhereAct = [
            'status' => StatusId::ACTUAL,
            'f_ini' => "'$inicurs_ca','$fincurs_ca'",
            'id_tipo_activ' => '^' . ConfigGlobal::mi_sfsv() . '(122)|(222)|(332)',
        ];
        $aOperadoresAct = [
            'f_ini' => 'BETWEEN',
            'id_tipo_activ' => '~',
        ];

        foreach ($cAlumnos as $oPersonaDl) {
            $id_nom = $oPersonaDl->getId_nom();
            $cAsistencias = [];
            if (empty($Qid_activ)) {
                $service = $GLOBALS['container']->get(AsistenteActividadService::class);
                $cAsistencias = $service->getActividadesDeAsistente(['id_nom' => $id_nom, 'propio' => 't'], [], $aWhereAct, $aOperadoresAct);
            } else {
                $AsistenteDlRepository = $GLOBALS['container']->get(AsistenteDlRepositoryInterface::class);
                $oAsistenteDl = $AsistenteDlRepository->findById($Qid_activ, $id_nom);
                $cAsistencias[0] = $oAsistenteDl;
            }

            switch (count($cAsistencias)) {
                case 0:
                    $msg .= sprintf(_('no se ha hecho nada con %s no tiene asignado ca'), $oPersonaDl->getPrefApellidosNombre()) . "\n";
                    break;
                case 1:
                    $oAsistenteDl = current($cAsistencias);
                    $id_activ_1 = $oAsistenteDl->getId_activ();
                    $est_ok = $oAsistenteDl->isEst_ok();
                    if ($est_ok) {
                        $msg .= sprintf(_('no se ha hecho nada com %s. ya tiene el plan de estudios confirmado'), $oPersonaDl->getPrefApellidosNombre()) . "\n";
                        break;
                    }

                    $MatriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
                    $cMatriculas = $MatriculaDlRepository->getMatriculas(['id_nom' => $id_nom, 'id_activ' => $id_activ_1]);
                    foreach ($cMatriculas as $oMatricula) {
                        if ($MatriculaDlRepository->Eliminar($oMatricula) === false) {
                            $msg .= _('hay un error, no se ha eliminado') . "\n" . $oMatricula->getErrorTxt() . "\n";
                        }
                    }

                    $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
                    $cPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $id_nom]);
                    $a_aprobadas = [];
                    foreach ($cPersonaNotas as $oPersonaNota) {
                        if ($oPersonaNota->isAprobada()) {
                            $a_aprobadas[] = $oPersonaNota->getId_asignatura();
                        }
                    }

                    $ActividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
                    $cAsignaturasCa = $ActividadAsignaturaRepository->getActividadAsignaturas(
                        ['id_activ' => $id_activ_1, 'tipo' => 'x'],
                        ['tipo' => 'IS NULL']
                    );

                    $m = 0;
                    foreach ($cAsignaturasCa as $oActividadAsignatura) {
                        $id_asignatura = $oActividadAsignatura->getId_asignatura();
                        $preceptor = ($oActividadAsignatura->getTipo() === 'p');
                        if (in_array($id_asignatura, $a_aprobadas, true)) {
                            continue;
                        }
                        if ($id_asignatura > 3000) {
                            $guardado = self::matricularOpcionalSiCabe(
                                $PersonaNotaDBRepository,
                                $MatriculaDlRepository,
                                $id_nom,
                                $id_asignatura,
                                $id_activ_1,
                                $preceptor
                            );
                            if ($guardado === 'error') {
                                $msg .= _('error al guardar la matrícula') . "\n";
                            } elseif ($guardado === 'ok') {
                                $m++;
                            }
                        } else {
                            $oMatricula = new Matricula();
                            $oMatricula->setId_activ($id_activ_1);
                            $oMatricula->setIdAsignaturaVo(AsignaturaId::fromNullableInt($id_asignatura));
                            $oMatricula->setId_nom($id_nom);
                            $oMatricula->setPreceptor($preceptor);
                            if ($MatriculaDlRepository->Guardar($oMatricula) === false) {
                                $msg .= _('error al guardar la matrícula') . "\n";
                            }
                            $m++;
                        }
                    }
                    $msg .= sprintf(_('%s se ha matriculado de %s asignaturas'), $oPersonaDl->getPrefApellidosNombre(), $m) . "\n";
                    break;
                default:
                    $msg .= sprintf(_('no se ha hecho nada con %s, tiene asignado más de un ca'), $oPersonaDl->getPrefApellidosNombre()) . "\n";
            }
        }

        if (empty($msg)) {
            $msg = _('no se ha hecho nada');
        }
        return $msg;
    }

    private static function matricularOpcionalSiCabe(
        $PersonaNotaDBRepository,
        $MatriculaDlRepository,
        int $id_nom,
        int $id_asignatura,
        int $id_activ,
        bool $preceptor
    ): string {
        // Devuelve 'ok' | 'skip' | 'error'
        $bloque = (int) substr((string) $id_asignatura, 1, 1);
        $aFiltro = [
            1 => ['id_nivel' => "'123[012]'", 'max' => 3],
            2 => ['id_nivel' => "'243[01234]'", 'max' => 5],
            3 => ['id_nivel' => "'123[012]|243[01234]'", 'max' => 8],
        ];
        if (!isset($aFiltro[$bloque])) {
            return 'skip';
        }
        $aWhereNota = [
            'id_nom' => $id_nom,
            'id_nivel' => $aFiltro[$bloque]['id_nivel'],
        ];
        $aOperadorNota = ['id_nivel' => '~'];
        $cPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhereNota, $aOperadorNota);
        if (!is_array($cPersonaNotas) || count($cPersonaNotas) >= $aFiltro[$bloque]['max']) {
            return 'skip';
        }
        $oMatricula = $MatriculaDlRepository->findById($id_activ, $id_asignatura, $id_nom);
        $oMatricula->setPreceptor($preceptor);
        if ($MatriculaDlRepository->Guardar($oMatricula) === false) {
            return 'error';
        }
        return 'ok';
    }
}
