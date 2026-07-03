<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\procesos\domain\contracts\PermUsuarioActividadRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';
$Qid_item = 0;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel) && is_string($a_sel[0])) { //vengo de un checkbox
    strtok($a_sel[0], "#");
    $tok = strtok("#");
    $Qid_item = is_string($tok) ? (int)$tok : 0;
}

$PermUsuarioActividadRepository = DependencyResolver::get(PermUsuarioActividadRepositoryInterface::class);
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