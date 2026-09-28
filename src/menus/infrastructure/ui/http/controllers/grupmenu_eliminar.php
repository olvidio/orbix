<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$error_txt = '';

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'grupmenu_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$id_grupmenu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_grupmenu');
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
