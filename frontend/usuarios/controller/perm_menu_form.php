<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $sel0 = usuarios_sel_first_item($a_sel);
    $Qid_usuario = usuarios_id_from_sel_item($sel0);
    $Qid_item = usuarios_id_from_sel_second($sel0);
}

$data = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/perm_menu_info', [
    'id_usuario' => $Qid_usuario,
    'id_item' => $Qid_item,
]));

$nombre = tessera_imprimir_string($data['nombre'] ?? '');
$menu_perm = tessera_imprimir_int($data['menu_perm'] ?? 0);
$menu_perm_dl_map = usuarios_perm_menu_dl_map_from_payload($data['menu_perm_dl_map'] ?? null);

$oHash = new HashFront();
$oHash->setCamposForm('menu_perm');
$oHash->setArraycamposHidden([
    'id_usuario' => $Qid_usuario,
    'id_item' => $Qid_item,
    'que' => 'perm_menu_update',
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'nombre' => $nombre,
    'oHash' => $oHash,
    'menu_perm_dl_map' => $menu_perm_dl_map,
    'menu_perm' => $menu_perm,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('perm_menu_form.phtml', $a_campos);
