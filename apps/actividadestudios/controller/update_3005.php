<?php

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$msg_err = '';
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qpau = (string)filter_input(INPUT_POST, 'pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    if ($Qpau === "a") {
        $Qid_activ = (integer)strtok($a_sel[0], "#");
        $Qid_asignatura = (integer)strtok("#");
    }
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
    $Qid_asignatura = (integer)filter_input(INPUT_POST, 'id_asignatura');
}

$msg_err = '';
$ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
switch ($Qmod) {
    case 'eliminar':  //------------ BORRAR --------
        if ($Qpau === "a") {
            $oActividadAsignatura = $ActividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
            if ($ActividadAsignaturaDlRepository->Elimiinar($oActividadAsignatura) === false) {
                $msg_err = _("hay un error, no se ha borrado");
            }
        }
        break;
    case 'nuevo': //------------ NUEVO --------
        $oActividadAsignatura = new ActividadAsignatura();
        $oActividadAsignatura->setId_activ($Qid_activ);
        $oActividadAsignatura->setId_asignatura($Qid_asignatura);

        $Qid_profesor = (integer)filter_input(INPUT_POST, 'id_profesor');
        $Qavis_profesor = (string)filter_input(INPUT_POST, 'avis_profesor');
        $Qtipo = (string)filter_input(INPUT_POST, 'tipo');
        $Qf_ini = (string)filter_input(INPUT_POST, 'f_ini');
        $Qf_fin = (string)filter_input(INPUT_POST, 'f_fin');

        $oActividadAsignatura->setId_profesor($Qid_profesor);
        $oActividadAsignatura->setAvis_profesor($Qavis_profesor);
        $oActividadAsignatura->setTipo($Qtipo);
        $oF_ini = DateTimeLocal::createFromLocal($Qf_ini);
        $oActividadAsignatura->setF_ini($oF_ini);
        $oF_fin = DateTimeLocal::createFromLocal($Qf_fin);
        $oActividadAsignatura->setF_fin($oF_fin);
        if ($ActividadAsignaturaDlRepository->Guardar($oActividadAsignatura) === false) {
            $msg_err = _("hay un error, no se ha creado");
        }
        // si es la primera asignatura, hay que abrir el dossier para esta actividad
        $DosierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $oDossier = $DosierRepository->findByPk(DossierPk::fromArray(['tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3005]));
        if ($oDossier === null) {
            $oDossier = new Dossier();
            $oDossier->setTabla('a');
            $oDossier->setId_pau($Qid_activ);
            $oDossier->setId_tipo_dossier(3005);
        }
        $oDossier->abrir();
        $DosierRepository->Guardar($oDossier);
        break;
    case 'editar': //------------ EDITAR --------
        $Qid_profesor = (integer)filter_input(INPUT_POST, 'id_profesor');
        $Qavis_profesor = (string)filter_input(INPUT_POST, 'avis_profesor');
        $Qtipo = (string)filter_input(INPUT_POST, 'tipo');
        $Qf_ini = (string)filter_input(INPUT_POST, 'f_ini');
        $Qf_fin = (string)filter_input(INPUT_POST, 'f_fin');

        $oActividadAsignatura = $ActividadAsignaturaDlRepository->findById($Qid_activ, $Qid_asignatura);
        $oActividadAsignatura->setId_profesor($Qid_profesor);
        $oActividadAsignatura->setAvis_profesor($Qavis_profesor);
        $oActividadAsignatura->setTipo($Qtipo);
        $oF_ini = DateTimeLocal::createFromLocal($Qf_ini);
        $oActividadAsignatura->setF_ini($oF_ini);
        $oF_fin = DateTimeLocal::createFromLocal($Qf_fin);
        $oActividadAsignatura->setF_fin($oF_fin);
        if ($ActividadAsignaturaDlRepository->Guardar($oActividadAsignatura) === false) {
            $msg_err = _("hay un error, no se ha guardado");
        }
        break;
}

if (empty($msg_err)) {
    echo $msg_err;
}
