<?php

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_role = (integer)strtok($a_sel[0], "#");
}
$RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
$oRole = $RoleRepository->findById($id_role);
if ($RoleRepository->Eliminar($oRole) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $RoleRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');