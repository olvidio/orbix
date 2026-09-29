<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$error_txt = '';

if (!empty($a_sel)) { //vengo de un checkbox
    $GrupMenuRoleRepository = DependencyResolver::get(GrupMenuRoleRepositoryInterface::class);
    foreach ($a_sel as $sel) {
        if (!is_string($sel) || $sel === '') {
            continue;
        }
        try {
            $ctx = HashB::open($sel, 'role_grupmenu_del');
        } catch (HashBInvalidException $e) {
            continue;
        }
        $id_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');
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