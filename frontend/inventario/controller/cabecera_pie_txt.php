<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/src/inventario/controller/cabecera_pie_txt.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$cabecera = $data['cabecera'];
$cabeceraB = $data['cabeceraB'];
$firma = $data['firma'];
$pie = $data['pie'];

$oHash = new Hash();
$oHash->setCamposForm('cabecera!cabeceraB!firma!pie');

$a_campos = [
    'oPosicion' => $oPosicion,
    'cabecera' => $cabecera,
    'cabeceraB' => $cabeceraB,
    'firma' => $firma,
    'pie' => $pie,
    'oHash' => $oHash,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('cabecera_pie_txt.phtml', $a_campos);