<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use permisos\model\PermDl;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
$oCuadros = new PermDl;

// FIN de  Cabecera global de URL de controlador ********************************

//$oPosicion->recordar();

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

$url = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/perm_menu_info.php'
);

$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario, 'id_item' => $Qid_item]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url, $hash_params);

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