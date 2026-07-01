<?php

namespace src\actividadestudios\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\config\ConfigGlobal;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\input_int;

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
    public function __construct(
        private PersonaExRepositoryInterface $personaExRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private AsistenteActividadService $asistenteActividadService,
        private AsistenteDlRepositoryInterface $asistenteDlRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private PersonaNotaRepositoryInterface $personaNotaRepository,
        private ActividadAsignaturaRepositoryInterface $actividadAsignaturaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $msg = '';

        $a_sel = (array) ($input['sel'] ?? []);
        $Qid_activ = 0;
        if (!empty($a_sel)) {
            $sel = $a_sel[0];
            $Qid_nom = (int) strtok(is_scalar($sel) ? (string) $sel : '', '#');
        } else {
            $Qid_nom = input_int($input, 'id_pau');
            $Qid_activ = input_int($input, 'id_activ');
        }

        $mes = (int) date('m');
        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        $fin_m = $oConfig->getMesFinStgr();
        $any = ($mes > $fin_m) ? (int) date('Y') + 1 : (int) date('Y');
        $inicurs_ca = \src\shared\domain\helpers\curso_est('inicio', $any)->format('Y-m-d');
        $fincurs_ca = \src\shared\domain\helpers\curso_est('fin', $any)->format('Y-m-d');

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
            $classname = PersonaRepositoryResolver::objPauFromInstance($oPersona);

            if ($classname === 'PersonaEx') {
                $cAlumnos = $this->personaExRepository->getPersonas($aWhere, $aOperador);
            } else {
                $cAlumnos = $this->personaDlRepository->getPersonas($aWhere, $aOperador);
            }
            if (empty($cAlumnos)) {
                $msg = _('está de repaso');
            }
        } else {
            $aWhere['situacion'] = 'A';
            $aWhere['nivel_stgr'] = NivelStgrId::R;
            $aOperador['nivel_stgr'] = '!=';
            $cAlumnos = $this->personaDlRepository->getPersonas($aWhere, $aOperador);
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
                $cAsistencias = $this->asistenteActividadService->getActividadesDeAsistente(['id_nom' => $id_nom, 'propio' => 't'], [], $aWhereAct, $aOperadoresAct);
            } else {
                $oAsistenteDl = $this->asistenteDlRepository->findById($Qid_activ, $id_nom);
                $cAsistencias[0] = $oAsistenteDl;
            }

            switch (count($cAsistencias)) {
                case 0:
                    $msg .= sprintf(_('no se ha hecho nada con %s no tiene asignado ca'), $oPersonaDl->getPrefApellidosNombre()) . "\n";
                    break;
                case 1:
                    $oAsistenteDl = current($cAsistencias);
                    if ($oAsistenteDl === null) {
                        $msg .= sprintf(_('no se ha hecho nada con %s no tiene asignado ca'), $oPersonaDl->getPrefApellidosNombre()) . "\n";
                        break;
                    }
                    $id_activ_1 = $oAsistenteDl->getId_activ();
                    $est_ok = $oAsistenteDl->isEst_ok();
                    if ($est_ok) {
                        $msg .= sprintf(_('no se ha hecho nada com %s. ya tiene el plan de estudios confirmado'), $oPersonaDl->getPrefApellidosNombre()) . "\n";
                        break;
                    }

                    $cMatriculas = $this->matriculaDlRepository->getMatriculas(['id_nom' => $id_nom, 'id_activ' => $id_activ_1]);
                    foreach ($cMatriculas as $oMatricula) {
                        if ($this->matriculaDlRepository->Eliminar($oMatricula) === false) {
                            $msg .= _('hay un error, no se ha eliminado') . "\n" . $this->matriculaDlRepository->getErrorTxt() . "\n";
                        }
                    }

                    $cPersonaNotas = $this->personaNotaRepository->getPersonaNotas(['id_nom' => $id_nom]);
                    $a_aprobadas = [];
                    foreach ($cPersonaNotas as $oPersonaNota) {
                        if ($oPersonaNota->isAprobada()) {
                            $a_aprobadas[] = $oPersonaNota->getId_asignatura();
                        }
                    }

                    $cAsignaturasCa = $this->actividadAsignaturaRepository->getActividadAsignaturas(
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
                            $guardado = $this->matricularOpcionalSiCabe(
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
                            $oMatricula->setIdAsignaturaVo(new AsignaturaId($id_asignatura));
                            $oMatricula->setId_nom($id_nom);
                            $oMatricula->setPreceptor($preceptor);
                            if ($this->matriculaDlRepository->Guardar($oMatricula) === false) {
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

    private function matricularOpcionalSiCabe(
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
        $cPersonaNotas = $this->personaNotaRepository->getPersonaNotas($aWhereNota, $aOperadorNota);
        if (count($cPersonaNotas) >= $aFiltro[$bloque]['max']) {
            return 'skip';
        }
        $oMatricula = $this->matriculaDlRepository->findById($id_activ, $id_asignatura, $id_nom);
        if ($oMatricula === null) {
            $oMatricula = new Matricula();
            $oMatricula->setId_activ($id_activ);
            $oMatricula->setIdAsignaturaVo(new AsignaturaId($id_asignatura));
            $oMatricula->setId_nom($id_nom);
        }
        $oMatricula->setPreceptor($preceptor);
        if ($this->matriculaDlRepository->Guardar($oMatricula) === false) {
            return 'error';
        }
        return 'ok';
    }
}
