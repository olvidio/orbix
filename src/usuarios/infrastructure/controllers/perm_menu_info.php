<?php

use src\usuarios\application\repositories\GrupoRepository;
use src\usuarios\domain\entity\PermMenu;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (int)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

$GrupoRepository = new GrupoRepository();
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