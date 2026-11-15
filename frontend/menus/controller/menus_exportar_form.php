<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

// La cápsula de exportar la firma `lista_templates`, como la de importar.
$data = PostRequest::getDataFromUrl('/src/menus/lista_templates');
$ctx_exportar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_exportar'] ?? '');

$url = AppUrlConfig::srcBrowserUrl('/src/menus/menus_exportar');
$oHash = new HashF();
$oHash->setUrl($url);
$oHash->setArrayCamposHidden(['sobreescribir' => 'false', 'ctx_exportar' => $ctx_exportar]);
$oHash->setCamposForm('nombre');
$oHash->setCamposNo('sobreescribir');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('menus_exportar_form.phtml', $a_campos);
