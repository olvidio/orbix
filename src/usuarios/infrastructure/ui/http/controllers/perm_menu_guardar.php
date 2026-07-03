<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\entity\PermMenu;
use src\shared\web\ContestarJson;

$error_txt = '';

$Qid_item = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_item');
$Qid_usuario = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_usuario');
$Qmenu_perm = (array)\src\shared\domain\helpers\FilterPostGet::post('menu_perm', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$PermMenuRepository = DependencyResolver::get(PermMenuRepositoryInterface::class);
if (empty($Qid_item)) {
    $id_item_new = $PermMenuRepository->getNewId();
    $oUsuarioPerm = new PermMenu();
    $oUsuarioPerm->setId_item($id_item_new);
} else {
    $oUsuarioPerm = $PermMenuRepository->findById($Qid_item);
    if ($oUsuarioPerm === null) {
        ContestarJson::enviar(_('Permiso de menú no encontrado'), 'none');
        return;
    }
}
$oUsuarioPerm->setId_usuario($Qid_usuario);
//cuando el campo es menu_perm, se pasa un array que hay que convertirlo en número.
if (!empty($Qmenu_perm)) {
    $byte = 0;
    foreach ($Qmenu_perm as $bit) {
        $byte = $byte + $bit;
    }
    $oUsuarioPerm->setMenu_perm($byte);
}
if ($PermMenuRepository->Guardar($oUsuarioPerm) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $PermMenuRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');