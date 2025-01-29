<?php

use core\ViewPhtml;
use permisos\model\PermDl;
use usuarios\model\entity\GrupoOUsuario;
use usuarios\model\entity\PermMenu;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************
$oCuadros = new PermDl;

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qquien = (string)filter_input(INPUT_POST, 'quien');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_usuario = (integer)strtok($a_sel[0], "#");
    $Qid_item = (integer)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$oUsuario = new GrupoOUsuario(array('id_usuario' => $Qid_usuario)); // La tabla y su heredada
$nombre = $oUsuario->getUsuario();

if (!empty($Qid_item)) {
    $oPermiso = new PermMenu(array('id_item' => $Qid_item));
    $menu_perm = $oPermiso->getMenu_perm();
} else { // es nuevo
    $oPermiso = new PermMenu(array('id_usuario' => $Qid_usuario));
    $menu_perm = 0;
}

$oHash = new Hash();
$oHash->setCamposForm('menu_perm');
$aCamposHidden = array(
    'id_usuario' => $Qid_usuario,
    'id_item' => $Qid_item,
    'que' => 'perm_menu_update',
    'quien' => $Qquien
);
$oHash->setArraycamposHidden($aCamposHidden);


$a_campos = ['oPosicion' => $oPosicion,
    'nombre' => $nombre,
    'oHash' => $oHash,
    'oCuadros' => $oCuadros,
    'menu_perm' => $menu_perm,
];

$oView = new ViewPhtml('usuarios/controller');
$oView->renderizar('usuario_perm_menu.phtml', $a_campos);