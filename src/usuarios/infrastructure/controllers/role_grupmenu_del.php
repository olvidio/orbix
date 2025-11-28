<?php

use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) { //vengo de un checkbox
    $GrupMenuRoleRepository = $GLOBALS['container']->get(GrupMenuRoleRepositoryInterface::class);
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