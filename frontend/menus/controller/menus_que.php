<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');


$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/menus/infrastructure/controllers/lista_grup_menus.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$aOpciones = $data['a_opciones'];

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

$oView = new ViewSrcPhtml('frontend/menus/controller');
$oView->renderizar('menus_que.phtml', $a_campos);