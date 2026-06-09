<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

// 12
$url_ctr = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_de_ctr.php?';
// 13
$url_dlb = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/inventario/controller/doc_de_dlb.php?';

$oHash = new HashFront();
$oHash->setArrayCamposHidden(['inventario' => 1]);

$a_campos = [
    'oHash' => $oHash,
    'url_ctr' => $url_ctr,
    'url_dlb' => $url_dlb,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('inventario_que.phtml', $a_campos);

