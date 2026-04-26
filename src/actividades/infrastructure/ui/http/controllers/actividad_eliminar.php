<?php
/**
 * Endpoint backend AJAX: elimina las actividades indicadas.
 *
 * Acepta dos formas de entrada:
 *  - `sel[]` con los ids (checkboxes de seleccion masiva).
 *  - `id_activ` unico cuando se viene del planning (borrar ficha concreta).
 *
 * Si la app `procesos` esta instalada, valida el permiso `borrar` via
 * `$_SESSION['oPermActividades']`. Responde JSON {success, mensaje?}.
 *
 * Extraido del antiguo dispatcher actividad_update.php (case 'eliminar').
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\shared\config\ConfigGlobal;
use src\actividades\application\BorrarActividad;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use frontend\shared\web\ContestarJson;

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';
$ActividadDlRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);

if (!empty($a_sel)) {
    foreach ($a_sel as $id) {
        $id_activ = (integer)strtok($id, '#');
        $oActividad = $ActividadDlRepository->findById($id_activ);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();

        if (ConfigGlobal::is_app_installed('procesos')) {
            $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
            $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
            if ($oPermActiv->have_perm_activ('borrar') === TRUE) {
                $error_txt .= BorrarActividad::ejecutar($id_activ);
            } else {
                $error_txt .= _("No tiene permiso para borrar esta actividad");
            }
        } else {
            $error_txt .= BorrarActividad::ejecutar($id_activ);
        }
    }
}

// si vengo desde la presentacion del planning, ya tengo el id_activ.
if (!empty($Qid_activ)) {
    $oActividad = $ActividadDlRepository->findById($Qid_activ);
    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $dl_org = $oActividad->getDl_org();

    if (ConfigGlobal::is_app_installed('procesos')) {
        $_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
        $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
        if ($oPermActiv->have_perm_activ('borrar') === TRUE) {
            $error_txt .= BorrarActividad::ejecutar($Qid_activ);
        } else {
            $error_txt .= _("No tiene permiso para borrar esta actividad");
        }
    } else {
        $error_txt .= BorrarActividad::ejecutar($Qid_activ);
    }
}

ContestarJson::enviar($error_txt);
