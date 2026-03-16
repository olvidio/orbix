<?php

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\entity\PermMenu;
use web\ContestarJson;

$Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

$GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
$oUsuario = $GrupoRepository->findById($Qid_usuario); // La tabla y su heredada
$nombre = $oUsuario->getUsuarioAsString();

if (!empty($Qid_item)) {
    $oPermiso = new PermMenu(array('id_item' => $Qid_item));
    $menu_perm = $oPermiso->getMenu_perm();
} else { // es nuevo
    $oPermiso = new PermMenu(array('id_usuario' => $Qid_usuario));
    $menu_perm = 0;
}

$error_txt = '';
$data['nombre'] = $nombre;
$data['menu_perm'] = $menu_perm;

ContestarJson::enviar($error_txt, $data);