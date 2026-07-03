<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $sel0 = UsuariosPostInput::selFirstItem($a_sel);
    $Qid_usuario = UsuariosPostInput::idFromSelItem($sel0);
    $Qid_item = UsuariosPostInput::idFromSelSecond($sel0);
}

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/perm_menu_info', [
    'id_usuario' => $Qid_usuario,
    'id_item' => $Qid_item,
]));

$nombre = PayloadCoercion::string($data['nombre'] ?? '');
$menu_perm = PayloadCoercion::int($data['menu_perm'] ?? 0);
$menu_perm_dl_map = UsuariosPayload::permMenuDlMapFromPayload($data['menu_perm_dl_map'] ?? null);

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
