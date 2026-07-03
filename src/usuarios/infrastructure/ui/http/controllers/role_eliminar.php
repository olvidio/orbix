<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';
$id_role = 0;

$a_sel = (array)FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel) && is_string($a_sel[0])) { //vengo de un checkbox
    $tok = strtok($a_sel[0], "#");
    $id_role = is_string($tok) ? (int)$tok : 0;
}
$RoleRepository = DependencyResolver::get(RoleRepositoryInterface::class);
$oRole = $RoleRepository->findById($id_role);
if ($oRole === null) {
    $error_txt .= _("no existe el registro");
} elseif ($RoleRepository->Eliminar($oRole) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $RoleRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');