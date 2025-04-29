<?php

use src\menus\application\repositories\GrupMenuRoleRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) { //vengo de un checkbox
    $GrupMenuRoleRepository = new GrupMenuRoleRepository();
    foreach ($a_sel as $sel) {
        $id_item = (integer)strtok($sel, "#");
        $oGrupMenuRole = $GrupMenuRoleRepository->findById($id_item);
        if ($GrupMenuRoleRepository->Eliminar($oGrupMenuRole) === false) {
            $error_txt .= _("hay un error, no se ha eliminado");
            $error_txt .= "\n" . $GrupMenuRoleRepository->getErrorTxt();
        }
    }
} else {
    $error_txt = _("debe seleccionar uno");
}

ContestarJson::enviar($error_txt, 'ok');