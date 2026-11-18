<?php

namespace src\actividadestudios\application;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\actividadestudios\domain\criteria\MatriculaField as MatF;
use src\actividadestudios\domain\criteria\ActividadAsignaturaField as AaF;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\criteria\ActividadCargoField;
use src\actividadcargos\domain\criteria\CargoField;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\personas\domain\entity\Persona;

/**
 * @return array{
 *   msg_err: string,
 *   nom_activ: string,
 *   nom_director_est: string,
 *   sin_director: bool,
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
     *   sin_director: bool,
     *   aPreceptores: array<int, array{nombre_corto: mixed, creditos: mixed, nom_profesor: string}>,
     *   aProfesores: array<int, array{nombre_corto: mixed, creditos: mixed, nom_profesor: string}>,
     *   aAlumnos: array<int, array{nom_persona: string, ctr: string, observ_est: mixed, aAsignaturas: mixed}>
     * }
     */
    public function execute(array $input): array
    {
        $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $msgErr = '';

        $oActividad = $this->actividadAllRepository->findById($idActiv);
        if ($oActividad === null) {
            return [
                'msg_err' => sprintf(_("No encuentro actividad con id: %d"), $idActiv),
                'nom_activ' => '',
                'nom_director_est' => '',
                'sin_director' => false,
                'aPreceptores' => [],
                'aProfesores' => [],
                'aAlumnos' => [],
            ];
        }
        $nomActiv = $oActividad->getNom_activ();

        $cCargos = $this->cargoRepository->matching(Criteria::create()->where(Filter::eq(CargoField::Cargo, 'd.est.')));
        $idNomDtorEst = 0;
        if ($cCargos === []) {
            $msgErr .= '<br>' . _("No existe el cargo «d.est.» en la tabla de cargos.");
        } else {
            $cActividadCargos = $this->actividadCargoRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(ActividadCargoField::IdActiv, $idActiv))
                    ->andWhere(Filter::eq(ActividadCargoField::IdCargo, $cCargos[0]->getId_cargo()))
            );
            if (count($cActividadCargos) > 0) {
                $idNomDtorEst = $cActividadCargos[0]->getId_nom();
            }
        }

        if ($idNomDtorEst <= 0) {
            $nomDirectorEst = _("para nombrarlo, ir al dossier de cargos de la actividad");
        } else {
            $oPersona = Persona::findPersonaEnGlobal($idNomDtorEst);
            if (!is_object($oPersona)) {
                $msgErr .= '<br>' . sprintf(_("No encuentro a nadie con id_nom: %s"), $idNomDtorEst);
                $nomDirectorEst = '';
            } else {
                $nomDirectorEst = $oPersona->getPrefApellidosNombre();
            }
        }

        $aPreceptores = [];
        $aProfesores = [];
        $a = 0;
        $cActividadAsignaturas = $this->actividadAsignaturaDlRepository->matching(
            Criteria::create()->where(Filter::eq(AaF::IdActiv, $idActiv))->orderBy(AaF::Tipo)
        );
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $a++;
            $idAsignatura = $oActividadAsignatura->getId_asignatura();
            $idProfesor = $oActividadAsignatura->getId_profesor();
            $tipo = $oActividadAsignatura->getTipo();

            $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
            if ($oAsignatura === null) {
                // Asignatura que ya no existe: se salta con un aviso, sin tumbar el listado.
                $msgErr .= '<br>' . sprintf(_("No se ha encontrado la asignatura con id: %s"), (string)$idAsignatura);
                continue;
            }
            $nombreCorto = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos();

            if (!empty($idProfesor)) {
                $oPersona = Persona::findPersonaEnGlobal($idProfesor);
                if (!is_object($oPersona)) {
                    // Como en la lista de clases: la asignatura sale con el profesor en blanco.
                    $msgErr .= '<br>' . sprintf(_("No encuentro a nadie con id_nom: %s"), $idProfesor);
                    $nomProfesor = '';
                } else {
                    $nomProfesor = $oPersona->getPrefApellidosNombre();
                }
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
            $idNom = $oAsistente->getId_nom();
            $observEst = $oAsistente->getObserv_est();
            $oPersona = Persona::findPersonaEnGlobal($idNom);
            if ($oPersona === null) {
                $msgErr .= '<br>' . sprintf(_("No encuentro a nadie con id_nom: %s"), $idNom);
                continue;
            }
            $a++; // después de encontrar a la persona: un alumno omitido no deja hueco
            $nomPersona = $oPersona->getPrefApellidosNombre();
            $ctr = $oPersona->getCentro_o_dl();
            $stgr = $oPersona->getNivel_stgr();

            $cMatriculas = $this->matriculaRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(MatF::IdNom, $idNom))
                    ->andWhere(Filter::eq(MatF::IdActiv, $idActiv))
            );
            if (count($cMatriculas) === 0) {
                switch ($stgr) {
                    case NivelStgrId::R:
                        $est = _("repaso");
                        break;
                    case NivelStgrId::N:
                        $est = _("plan de formación");
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
                    $idAsignatura = $oMatricula->getId_asignatura();
                    $preceptor = $oMatricula->isPreceptor();

                    $oAsignatura = $this->asignaturaRepository->findById($idAsignatura);
                    if ($oAsignatura === null) {
                        $msgErr .= '<br>' . sprintf(_("No se ha encontrado la asignatura con id: %s"), (string)$idAsignatura);
                        continue;
                    }
                    $i++;
                    $nombreCorto = $oAsignatura->getNombre_corto();
                    $creditos = $oAsignatura->getCreditos();
                    $preceptorTxt = \src\shared\domain\helpers\FuncTablasSupport::isTrue($preceptor) ? '(' . _("preceptor") . ')' : '';

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
            'sin_director' => $idNomDtorEst <= 0,
            'aPreceptores' => $aPreceptores,
            'aProfesores' => $aProfesores,
            'aAlumnos' => $aAlumnos,
        ];
    }
}
