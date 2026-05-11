<?php

use src\permisos\domain\MenuDlPermissionBits;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use frontend\shared\web\ContestarJson;

$Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

$GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
$oUsuario = $GrupoRepository->findById($Qid_usuario); // La tabla y su heredada
$nombre = $oUsuario->getUsuarioAsString();

$PermMenuRepository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
if (!empty($Qid_item)) {
    $oPermiso = $PermMenuRepository->findById(['id_item' => $Qid_item]);
    $menu_perm = $oPermiso->getMenu_perm();
} else { // es nuevo
    $menu_perm = 0;
}

$error_txt = '';
$data['nombre'] = $nombre;
$data['menu_perm'] = $menu_perm;
$data['menu_perm_dl_map'] = MenuDlPermissionBits::map();

ContestarJson::enviar($error_txt, $data);