<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');


$url_backend = '/src/menus/infrastructure/controllers/grupmenu_lista.php';
$data = PostRequest::getDataFromUrl($url_backend);

$aOpciones = $data['a_lista'];

$oDesplGM = new Desplegable('', $aOpciones, '', true);
$oDesplGM->setOpcion_sel($Qfiltro_grupo);
$oDesplGM->setAction('fnjs_lista_menus()');
$oDesplGM->setNombre('filtro_grupo');

$url = ConfigGlobal::getWeb() . '/frontend/menus/controller/menus_get.php';
$oHash1 = new Hash();
$oHash1->setUrl($url);
$oHash1->setCamposForm('filtro_grupo');
$h1 = $oHash1->linkSinVal();

$a_campos = ['url' => $url,
    'h1' => $h1,
    'oDesplGM' => $oDesplGM,
];

$oView = new ViewNewPhtml('frontend/menus/controller');
$oView->renderizar('menus_que.phtml', $a_campos);