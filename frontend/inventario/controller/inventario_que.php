<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


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

