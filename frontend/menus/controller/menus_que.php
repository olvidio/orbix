<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');


$url_backend = '/src/menus/grupmenu_lista';
$data = PostRequest::getDataFromUrl($url_backend);

$aOpciones = NotasFormSupport::desplegableOpciones($data['a_lista'] ?? []);

$oDesplGM = new Desplegable('', $aOpciones, '', true);
$oDesplGM->setOpcion_sel($Qfiltro_grupo);
$oDesplGM->setAction('fnjs_lista_menus()');
$oDesplGM->setNombre('filtro_grupo');

$url = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/menus/controller/menus_get.php';
$oHash1 = new HashFront();
$oHash1->setUrl($url);
$oHash1->setCamposForm('filtro_grupo');
$h1 = $oHash1->linkSinValParams();

$a_campos = ['url' => $url,
    'h1' => $h1,
    'oDesplGM' => $oDesplGM,
];

$oView = new ViewNewPhtml('frontend/menus/controller');
$oView->renderizar('menus_que.phtml', $a_campos);