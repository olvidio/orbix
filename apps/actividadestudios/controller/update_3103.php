<?php

use actividadestudios\model\entity\ActividadAsignaturaDl;
use actividadestudios\model\entity\GestorActividadAsignatura;
use actividadestudios\model\entity\GestorActividadAsignaturaDl;
use actividadestudios\model\entity\GestorMatricula;
use actividadestudios\model\entity\MatriculaDl;
use dossiers\model\entity\Dossier;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qest_ok = (string)filter_input(INPUT_POST, 'est_ok');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qobserv_est = (string)filter_input(INPUT_POST, 'observ_est');

//En el caso de eliminar desde la lista de cargos
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    if ($Qpau === "p") {
        $Qid_activ = (integer)strtok($a_sel[0], "#");
        $Qid_asignatura = (integer)strtok("#");
        $Qid_nom = (integer)filter_input(INPUT_POST, 'id_pau');
    }
    if ($Qpau === "a") {
        $Qid_nom = (integer)strtok($a_sel[0], "#");
        $Qid_asignatura = (integer)strtok("#");
        $Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');
    }
} else { // desde el formulario
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_pau');
    $Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
    $Qid_nivel = (integer)filter_input(INPUT_POST, 'id_nivel');
    $Qid_situacion = (integer)filter_input(INPUT_POST, 'id_situacion');
    $Qpreceptor = (string)filter_input(INPUT_POST, 'preceptor');
    $Qid_preceptor = (integer)filter_input(INPUT_POST, 'id_preceptor');
}

$service = $GLOBALS['container']->get(AsistenteActividadService::class);
$AsistenteRepositoryInterface = $service->getRepoAsistente($Qid_nom, $Qid_activ);
$AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
$oAsistente = $AsistenteRepository->findById($Qid_activ, $Qid_nom);
switch ($Qmod) {
    case 'observ_est':  //------------ observaciones estudios --------
        $oAsistente->setObserv_est($Qobserv_est);
        $AsistenteRepository->Guardar($oAsistente);
        break;
    case 'observ':  //------------ observaciones --------
        $oAsistente->setObserv($Qobserv);
        $AsistenteRepository->Guardar($oAsistente);
        break;
    case 'plan':  //------------ confirmar estudios --------
        if (is_true($Qest_ok)) {
            $est_ok = 't';
        } else {
            $est_ok = 'f';
        }
        $oAsistente->setEst_ok($est_ok);
        $AsistenteRepository->Guardar($oAsistente);
        break;
    case 'eliminar': //------------ BORRAR --------
        if ($Qpau === "p") {
            // Para borrar varios
            foreach ($a_sel as $sel) {
                $id_activ = (integer)strtok($sel, '#');
                $id_asignatura = (integer)strtok('#');
                if (!empty($Qid_activ)) {
                    $id_activ = $Qid_activ;
                }
                if (!empty($Qid_nom)) {
                    $id_nom = $Qid_nom;
                } else {
                    $id_nom = (integer)strtok('#');
                }

                $oMatricula = new MatriculaDl(array('id_activ' => $id_activ, 'id_nom' => $id_nom, 'id_asignatura' => $id_asignatura));
                if ($oMatricula->DBEliminar() === false) {
                    $msg_err = _("hay un error, no se ha borrado");
                }
                // hay que cerrar el dossier para esta persona, si no tiene más actividades:
                $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
                $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $id_nom, 'id_tipo_dossier' => 1303]));
                $oDossier->cerrar();
                $DosierRepository->Guardar($oDossier);
                // Si la puse yo, hay que eliminar esta asignatura a las asignaturas que se dan en el ca
                // si no hay nadie más matriculado:
                $oGesActividadAsignatura = new GestorActividadAsignaturaDl();
                $cActividadAsignaturas = $oGesActividadAsignatura->getActividadAsignaturas(array('id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura));
                if (count($cActividadAsignaturas) === 1) {
                    $gesMatriculas = new GestorMatricula();
                    $cMatriculas = $gesMatriculas->getMatriculas(['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]);
                    if (count($cMatriculas) === 0) {
                        $oActividadAsignatura = $cActividadAsignaturas[0];
                        $oActividadAsignatura->DBEliminar();
                    }
                }
            }
        }
        if ($Qpau === "a") {
            $oMatricula = new MatriculaDl(array('id_activ' => $id_activ, 'id_nom' => $id_nom, 'id_asignatura' => $id_asignatura));
            if ($oMatricula->DBEliminar() === false) {
                $msg_err = _("hay un error, no se ha borrado");
            }
            // hay que cerrar el dossier para esta actividad, si no tiene más personas:
            $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
            $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'a', 'id_pau' => $id_activ, 'id_tipo_dossier' => 3103]));
            $oDossier->cerrar();
            $DosierRepository->Guardar($oDossier);
        }
        break;
    case 'nuevo': //------------ NUEVO --------
        // Si no es opcional, calculo el id_asignatura a partir del id_nivel
        if ($Qid_asignatura === 1) {
            $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
            $cAsignaturas = $AsignaturaRepository->getAsignaturas(array('id_nivel' => $Qid_nivel));
            $oAsignatura = $cAsignaturas[0]; // sólo debería haber una
            $Qid_asignatura = $oAsignatura->getId_asignatura();
        }

        $oMatricula = new MatriculaDl(array('id_activ' => $Qid_activ, 'id_nom' => $Qid_nom, 'id_asignatura' => $Qid_asignatura));
        $oMatricula->setId_nivel($Qid_nivel);
        $oMatricula->setId_situacion($Qid_situacion);
        empty($Qpreceptor) ? $oMatricula->setPreceptor('f') : $oMatricula->setPreceptor('t');
        $oMatricula->setId_preceptor($Qid_preceptor);
        if ($oMatricula->DBGuardar() === false) {
            $msg_err = _("hay un error, no se ha guardado");
        } else {
            // si no está abierto, hay que abrir el dossier para esta persona
            $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
            $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1303]));
            $oDossier->abrir();
            $DosierRepository->Guardar($oDossier);
            // ... y si es la primera persona, hay que abrir el dossier para esta actividad
            $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3103]));
            $oDossier->cerrar();
            $DosierRepository->Guardar($oDossier);

            // hay que añadir esta asignatura a las asignaturas que se dan en el ca
            // compruebo que no existe:
            $oGesActividadAsignatura = new GestorActividadAsignatura();
            $cActividadAsignaturas = $oGesActividadAsignatura->getActividadAsignaturas(array('id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura));
            if (count($cActividadAsignaturas) === 0) {
                $oActividadAsignatura = new ActividadAsignaturaDl();
                $oActividadAsignatura->setId_activ($Qid_activ);
                $oActividadAsignatura->setId_asignatura($Qid_asignatura);
                if (is_true($Qpreceptor)) {
                    $oActividadAsignatura->setId_profesor($Qid_preceptor);
                    $tipo = 'p';
                } else {
                    $tipo = '';
                }
                $oActividadAsignatura->setTipo($tipo);
                $oActividadAsignatura->DBGuardar();
            }
        }
        break;
    case 'editar':  //------------ EDITAR --------
        $oMatricula = new MatriculaDl(array('id_activ' => $Qid_activ, 'id_nom' => $Qid_nom, 'id_asignatura' => $Qid_asignatura));
        isset($Qid_asignatura) ? $oMatricula->setId_asignatura($Qid_asignatura) : $oMatricula->setId_asignatura();
        isset($Qid_nivel) ? $oMatricula->setId_nivel($Qid_nivel) : $oMatricula->setId_nivel();
        isset($Qid_situacion) ? $oMatricula->setId_situacion($Qid_situacion) : $oMatricula->setId_situacion();
        empty($Qpreceptor) ? $oMatricula->setPreceptor('f') : $oMatricula->setPreceptor('t');
        isset($Qid_preceptor) ? $oMatricula->setId_preceptor($Qid_preceptor) : $oMatricula->setId_preceptor();

        if ($oMatricula->DBGuardar() === false) {
            $msg_err = _("hay un error, no se ha guardado");
        }
}

if (!empty($msg_err)) {
    echo $msg_err;
}
