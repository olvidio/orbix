<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (integer)strtok("#");
}

$url_backend = '/src/usuarios/perm_menu_info';
$a_campos_backend = ['id_usuario' => $Qid_usuario, 'id_item' => $Qid_item];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$nombre = $data['nombre'];
$menu_perm = (int)$data['menu_perm'];
$menu_perm_dl_map = [];
if (isset($data['menu_perm_dl_map']) && is_array($data['menu_perm_dl_map'])) {
    $menu_perm_dl_map = $data['menu_perm_dl_map'];
}

$oHash = new HashFront();
$oHash->setCamposForm('menu_perm');
$aCamposHidden = array(
    'id_usuario' => $Qid_usuario,
    'id_item' => $Qid_item,
    'que' => 'perm_menu_update',
);
$oHash->setArraycamposHidden($aCamposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'nombre' => $nombre,
    'oHash' => $oHash,
    'menu_perm_dl_map' => $menu_perm_dl_map,
    'menu_perm' => $menu_perm,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('perm_menu_form.phtml', $a_campos);