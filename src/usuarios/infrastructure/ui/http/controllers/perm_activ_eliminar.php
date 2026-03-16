<?php

use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (integer)strtok("#");
}

$PermUsuarioActividadRepository = $GLOBALS['container']->get(PermUsuarioActividadRepositoryInterface::class);
$oPermUsuarioActividad = $PermUsuarioActividadRepository->findById($Qid_item);
if ($oPermUsuarioActividad === null) {
    $error_txt .= _("no existe el registro");
} else {
    if ($PermUsuarioActividadRepository->Eliminar($oPermUsuarioActividad) === false) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $PermUsuarioActividadRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');