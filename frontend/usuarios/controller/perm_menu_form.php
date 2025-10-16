<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use permisos\model\PermDl;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************
$oCuadros = new PermDl;

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (integer)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$url_backend = '/src/usuarios/infrastructure/controllers/perm_menu_info.php';
$a_campos = ['id_usuario' => $Qid_usuario, 'id_item' => $Qid_item];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos);

$nombre = $data['nombre'];
$menu_perm = $data['menu_perm'];

$oHash = new Hash();
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
    'oCuadros' => $oCuadros,
    'menu_perm' => $menu_perm,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('perm_menu_form.phtml', $a_campos);