<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url = AppUrlConfig::getApiBaseUrl() . '/src/menus/menus_exportar';
$oHash = new HashFront();
$oHash->setUrl($url);
$oHash->setArrayCamposHidden(['sobreescribir' => 'false']);
$oHash->setCamposForm('nombre');
$oHash->setCamposNo('sobreescribir');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('menus_exportar_form.phtml', $a_campos);
