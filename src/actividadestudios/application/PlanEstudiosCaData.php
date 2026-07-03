<?php

namespace src\actividadestudios\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\entity\Persona;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * @return array{
 *   msg_err: string,
 *   nom_activ: string,
 *   nom_director_est: string,
 *   aPreceptores: array<int, array{nombre_corto: mixed, creditos: mixed, nom_profesor: string}>,
 *   aProfesores: array<int, array{nombre_corto: mixed, creditos: mixed, nom_profesor: string}>,
 *   aAlumnos: array<int, array{nom_persona: string, ctr: string, observ_est: mixed, aAsignaturas: mixed}>
 * }
 */
final class PlanEstudiosCaData
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private CargoRepositoryInterface $cargoRepository,
        private ActividadCargoRepositoryInterface $actividadCargoRepository,
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private AsistenteActividadService $asistenteActividadService,
        private MatriculaRepositoryInterface $matriculaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   msg_err: string,
     *   nom_activ: string,
     *   nom_director_est: string,
     *   aPreceptores: array<int, array{nombre_corto: mixed, creditos: mixed, nom_profesor: string}>,
     *   aProfesores: array<int, array{nombre_corto: mixed, creditos: mixed, nom_profesor: string}>,
     *   aAlumnos: array<int, array{nom_persona: string, ctr: string, observ_est: mixed, aAsignaturas: mixed}>
     * }
     */
    public function execute(array $input): array
    {
        $idActiv = FuncTablasSupport::inputInt($input, 'id_activ');
        $msgErr = '';

        $oActividad = $this->actividadAllRepository->findById($idActiv);
        if ($oActividad === null) {
            return [
                'msg_err' => sprintf(_('No encuentro actividad con id: %d'), $idActiv),
                'nom_activ' => '',
                'nom_director_est' => '',
                'aPreceptores' => [],
                'aProfesores' => [],
                'aAlumnos' => [],
            ];
        }
        $nomActiv = $oActividad->getNom_activ();

        $cCargos = $this->cargoRepository->getCargos(['cargo' => 'd.est.']);
        $idCargo = $cCargos[0]->getId_cargo();
        $cActividadCargos = $this->actividadCargoRepository->getActividadCargos(['id_activ' => $idActiv, 'id_cargo' => $idCargo]);
        $idNomDtorEst = 0;
        if (count($cActividadCargos) > 0) {
            $idNomDtorEst = $cActividadCargos[0]->getId_nom();
        }

        if ($idNomDtorEst <= 0) {
            $nomDirectorEst = _('para nombrarlo, ir al dossier de cargos de la actividad');
        } else {
            $oPersona = Persona::findPersonaEnGlobal($idNomDtorEst);
            if (!is_object($oPersona)) {
                $msgErr .= "<br>No encuentro a nadie con id_nom: $idNomDtorEst en  " . __FILE__ . ': line ' . __LINE__;
                $nomDirectorEst = '';
            } else {
                $nomDirectorEst = $oPersona->getPrefApellidosNombre();
            }
        }

        $aPreceptores = [];
        $aProfesores = [];
        $a = 0;
        $cActividadAsignaturas = $this->actividadAsignaturaDlRepository->getActividadAsignaturas(['id_activ' => $idActiv, '_ordre' => 'tipo']);
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $a++;
            $idAsignatura = $oActividadAsignatura->getId_asignatura();
            $idProfesor = $oActividadAsignatura->getId_profesor();
            $tipo = $oActividadAsignatura->getTipo();

            $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignatura));
            }
            $nombreCorto = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos();

            if (!empty($idProfesor)) {
                $oPersona = Persona::findPersonaEnGlobal($idProfesor);
                if (!is_object($oPersona)) {
                    $msgErr .= "<br>No encuentro a nadie con id_nom: $idProfesor en  " . __FILE__ . ': line ' . __LINE__;
                    continue;
                }
                $nomProfesor = $oPersona->getPrefApellidosNombre();
            } else {
                $nomProfesor = '?';
            }

            if ($tipo === 'p') {
                $aPreceptores[$a] = [
                    'nombre_corto' => $nombreCorto,
                    'creditos' => $creditos,
                    'nom_profesor' => $nomProfesor,
                ];
            } else {
                $aProfesores[$a] = [
                    'nombre_corto' => $nombreCorto,
                    'creditos' => $creditos,
                    'nom_profesor' => $nomProfesor,
                ];
            }
        }

        $cAsistentes = $this->asistenteActividadService->getAsistentesDeActividad($idActiv);
        $a = 0;
        $aAlumnos = [];
        foreach ($cAsistentes as $oAsistente) {
            if (!$oAsistente->isPropio()) {
                continue;
            }
            $a++;
            $idNom = $oAsistente->getId_nom();
            $observEst = $oAsistente->getObserv_est();
            $oPersona = Persona::findPersonaEnGlobal($idNom);
            if ($oPersona === null) {
                $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom en  " . __FILE__ . ': line ' . __LINE__;
                continue;
            }
            $nomPersona = $oPersona->getPrefApellidosNombre();
            $ctr = $oPersona->getCentro_o_dl();
            $stgr = $oPersona->getNivel_stgr();

            $cMatriculas = $this->matriculaRepository->getMatriculas(['id_nom' => $idNom, 'id_activ' => $idActiv]);
            if (count($cMatriculas) === 0) {
                switch ($stgr) {
                    case 'r':
                        $est = _('repaso');
                        break;
                    case 'n':
                        $est = _('plan de formación');
                        break;
                    default:
                        $est = '???';
                }
                $aAlumnos[$a] = [
                    'nom_persona' => $nomPersona,
                    'ctr' => $ctr,
                    'observ_est' => $observEst,
                    'aAsignaturas' => $est,
                ];
            } else {
                $aAsignaturas = [];
                $i = 0;
                foreach ($cMatriculas as $oMatricula) {
                    $i++;
                    $idAsignatura = $oMatricula->getId_asignatura();
                    $preceptor = $oMatricula->isPreceptor();

                    $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
                    if ($oAsignatura === null) {
                        throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignatura));
                    }
                    $nombreCorto = $oAsignatura->getNombre_corto();
                    $creditos = $oAsignatura->getCreditos();
                    $preceptorTxt = FuncTablasSupport::isTrue($preceptor) ? '(' . _('preceptor') . ')' : '';

                    $aAsignaturas[$i] = [
                        'nombre_corto' => $nombreCorto,
                        'creditos' => $creditos,
                        'preceptor' => $preceptorTxt,
                    ];
                }
                $aAlumnos[$a] = [
                    'nom_persona' => $nomPersona,
                    'ctr' => $ctr,
                    'observ_est' => $observEst,
                    'aAsignaturas' => $aAsignaturas,
                ];
            }
        }

        return [
            'msg_err' => $msgErr,
            'nom_activ' => $nomActiv,
            'nom_director_est' => $nomDirectorEst,
            'aPreceptores' => $aPreceptores,
            'aProfesores' => $aProfesores,
            'aAlumnos' => $aAlumnos,
        ];
    }
}
