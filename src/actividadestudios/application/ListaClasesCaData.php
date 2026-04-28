<?php

namespace src\actividadestudios\application;

use frontend\shared\config\OrbixRuntime;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\personas\domain\entity\Persona;

/**
 * @return array{
 *   msg_err: string,
 *   nom_activ: string,
 *   nom_director_est: string,
 *   datos_asignatura: array<int, array{nom_profesor: string, tipo_profesor: string, nombre_corto: mixed, alumnos: array<string, string>}>
 * }
 */
final class ListaClasesCaData
{
    public static function execute(int $idActiv): array
    {
        $msgErr = '';

        $actividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $actividadAllRepository->findById($idActiv);
        $nomActiv = $oActividad->getNom_activ();
        $dlOrg = $oActividad->getDl_org();

        $cargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $cCargos = $cargoRepository->getCargos(['cargo' => 'd.est.']);
        $idCargo = $cCargos[0]->getId_cargo();
        $actividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $cActividadCargos = $actividadCargoRepository->getActividadCargos(['id_activ' => $idActiv, 'id_cargo' => $idCargo]);
        $idNomDtorEst = '';
        if (is_array($cActividadCargos) && !empty($cActividadCargos)) {
            $idNomDtorEst = $cActividadCargos[0]->getId_nom();
        }

        if ($idNomDtorEst === '') {
            $nomDirectorEst = '<span class=no_print>' . _('para nombrarlo, ir al dossier de cargos de la actividad') . '</span>';
        } else {
            $oPersona = Persona::findPersonaEnGlobal($idNomDtorEst);
            if (!is_object($oPersona)) {
                $msgErr .= "<br>No encuentro a nadie con id_nom: $idNomDtorEst en  " . __FILE__ . ': line ' . __LINE__;
                $nomDirectorEst = '';
            } else {
                $nomDirectorEst = $oPersona->getPrefApellidosNombre();
            }
        }

        $a = 0;
        $actividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
        $cActividadAsignaturas = $actividadAsignaturaRepository->getActividadAsignaturas(['id_activ' => $idActiv]);
        $datosAsignatura = [];
        $asignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $matriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $a++;
            $idAsignatura = $oActividadAsignatura->getId_asignatura();
            $tipo = $oActividadAsignatura->getTipo();
            $idProfesor = $oActividadAsignatura->getId_profesor();

            $oAsignatura = $asignaturaRepository->findById($idAsignatura);
            if ($oAsignatura === null) {
                throw new \RuntimeException(sprintf(_('No se ha encontrado la asignatura con id: %s'), (string)$idAsignatura));
            }
            $nombreCorto = $oAsignatura->getNombre_corto();
            if (!empty($idProfesor)) {
                $oPersona = Persona::findPersonaEnGlobal($idProfesor);
                if (!is_object($oPersona)) {
                    $msgErr .= "<br>No encuentro a nadie con id_nom: $idProfesor (profesor) en  " . __FILE__ . ': line ' . __LINE__;
                    $nomProfesor = '';
                } else {
                    $nomProfesor = $oPersona->getPrefApellidosNombre();
                }
            } else {
                $nomProfesor = '';
            }
            if (!empty($tipo) && $tipo === 'p') {
                $tipoProfesor = ucfirst(_('preceptor'));
            } else {
                $tipoProfesor = ucfirst(_('profesor'));
            }

            $cMatriculas = $matriculaRepository->getMatriculas(['id_activ' => $idActiv, 'id_asignatura' => $idAsignatura]);
            $aMatriculados = [];
            foreach ($cMatriculas as $oMatricula) {
                $idNom = $oMatricula->getId_nom();
                $oPersona = Persona::findPersonaEnGlobal($idNom);
                if ($oPersona === null) {
                    if ($dlOrg == OrbixRuntime::miDelef()) {
                        $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom en  " . __FILE__ . ': line ' . __LINE__;
                    }
                    continue;
                }
                $nomPersona = $oPersona->getPrefApellidosNombre();
                $ctr = $oPersona->getCentro_o_dl();
                $aMatriculados[$nomPersona] = $ctr;
            }
            uksort($aMatriculados, 'src\shared\domain\helpers\strsinacentocmp');

            $datosAsignatura[$a] = [
                'nom_profesor' => $nomProfesor,
                'tipo_profesor' => $tipoProfesor,
                'nombre_corto' => $nombreCorto,
                'alumnos' => $aMatriculados,
            ];
        }

        return [
            'msg_err' => $msgErr,
            'nom_activ' => $nomActiv,
            'nom_director_est' => $nomDirectorEst,
            'datos_asignatura' => $datosAsignatura,
        ];
    }
}
