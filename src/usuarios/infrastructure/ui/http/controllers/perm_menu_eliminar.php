<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$error_txt = '';

$a_sel = (array)\src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($a_sel === [] || !is_string($a_sel[0])) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

try {
    $ctx = HashB::open($a_sel[0], 'perm_menu_eliminar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');

$PermMenuRepository = DependencyResolver::get(PermMenuRepositoryInterface::class);
$oUsuarioPerm = $PermMenuRepository->findById($Qid_item);
if ($oUsuarioPerm === null) {
    $error_txt .= _("no existe el registro");
} elseif ($PermMenuRepository->Eliminar($oUsuarioPerm) === false) {
    $error_txt .= _("hay un error, no se ha eliminado");
    $error_txt .= "\n" . $PermMenuRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');
