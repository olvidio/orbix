<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';
$id_grupmenu = 0;

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel) && is_string($a_sel[0])) {
    $token = strtok($a_sel[0], "#");
    $id_grupmenu = is_numeric($token) ? (int) $token : 0;
}
/** @var GrupMenuRepositoryInterface $GrupMenuRepository */
$GrupMenuRepository = DependencyResolver::get(GrupMenuRepositoryInterface::class);
if ($id_grupmenu < 1) {
    ContestarJson::enviar(_("No encuentro el grupmenu"), 'ok');
    return;
}
$oGrupMenu = $GrupMenuRepository->findById($id_grupmenu);
if ($oGrupMenu === null) {
    ContestarJson::enviar(_("No encuentro el grupmenu"), 'ok');
    return;
}
if ($GrupMenuRepository->Eliminar($oGrupMenu) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $GrupMenuRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
