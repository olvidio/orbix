<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url = ConfigGlobal::getWeb() . '/src/menus/infrastructure/controllers/menus_exportar.php';
$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setArrayCamposHidden(['sobreescribir' => 'false']);
$oHash->setCamposForm('nombre');
$oHash->setCamposNo('sobreescribir');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
];

$oView = new ViewPhtml('../frontend/menus/controller');
$oView->renderizar('menus_exportar_form.phtml', $a_campos);
