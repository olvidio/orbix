<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$url = AppUrlConfig::srcBrowserUrl('/src/menus/menus_exportar');
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
