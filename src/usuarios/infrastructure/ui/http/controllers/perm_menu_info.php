<?php
use src\shared\infrastructure\DependencyResolver;

use src\permisos\domain\MenuDlPermissionBits;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_usuario = (int)filter_post('id_usuario');
$Qid_item = (int)filter_post('id_item');

$GrupoRepository = DependencyResolver::get(GrupoRepositoryInterface::class);
$oUsuario = $GrupoRepository->findById($Qid_usuario); // La tabla y su heredada
if ($oUsuario === null) {
    ContestarJson::enviar(_('Grupo no encontrado'), 'none');
    return;
}
$nombre = $oUsuario->getUsuarioAsString();

$PermMenuRepository = DependencyResolver::get(PermMenuRepositoryInterface::class);
if (!empty($Qid_item)) {
    $oPermiso = $PermMenuRepository->findById($Qid_item);
    $menu_perm = $oPermiso !== null ? $oPermiso->getMenu_perm() : 0;
} else { // es nuevo
    $menu_perm = 0;
}

$error_txt = '';
$data['nombre'] = $nombre;
$data['menu_perm'] = $menu_perm;
$data['menu_perm_dl_map'] = MenuDlPermissionBits::map();

ContestarJson::enviar($error_txt, $data);