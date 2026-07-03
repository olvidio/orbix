<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\shared\web\ContestarJson;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) { //vengo de un checkbox
    $GrupMenuRoleRepository = DependencyResolver::get(GrupMenuRoleRepositoryInterface::class);
    foreach ($a_sel as $sel) {
        if (!is_string($sel)) {
            continue;
        }
        $tok = strtok($sel, "#");
        $id_item = is_string($tok) ? (int)$tok : 0;
        $oGrupMenuRole = $GrupMenuRoleRepository->findById($id_item);
        if ($oGrupMenuRole === null) {
            $error_txt .= _("no existe el registro");
        } elseif ($GrupMenuRoleRepository->Eliminar($oGrupMenuRole) === false) {
            $error_txt .= _("hay un error, no se ha eliminado");
            $error_txt .= "\n" . $GrupMenuRoleRepository->getErrorTxt();
        }
    }
} else {
    $error_txt = _("debe seleccionar uno");
}

ContestarJson::enviar($error_txt, 'ok');