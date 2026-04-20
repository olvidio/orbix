<?php
/**
 * Endpoint backend AJAX: duplica la primera actividad seleccionada dentro de
 * la propia delegacion (o de la sf si el usuario tiene permiso `des`).
 * Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'duplicar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (empty($a_sel)) {
    $error_txt = _("no se ha seleccionado ninguna actividad");
} else {
    $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
    $id_activ = (integer)strtok($a_sel[0], '#');
    $oActividadAll = $ActividadDlRepository->findById($id_activ);
    $dl = $oActividadAll->getDl_org();
    // des si puede duplicar sf.
    if ($dl === ConfigGlobal::mi_delef() ||
        ($_SESSION['oPerm']->have_perm_oficina('des') && $dl === ConfigGlobal::mi_dele() . 'f')
    ) {
        $oActividad = $ActividadDlRepository->findById($id_activ);
        $newId = $ActividadDlRepository->getNewId();
        $newIdActiv = $ActividadDlRepository->getNewIdActividad($newId);
        $oActividad->setId_activ($newIdActiv);
        $nom = _("dup") . ' ' . $oActividad->getNom_activ();
        $oActividad->setNom_activ($nom);
        $oActividad->setStatus(StatusId::PROYECTO);
        if ($ActividadDlRepository->Guardar($oActividad) === false) {
            $error_txt = _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $oActividad->getErrorTxt();
        }
    } else {
        $error_txt = _("no se puede duplicar actividades que no sean de la propia dl");
    }
}

ContestarJson::enviar($error_txt);
