<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$url_backend = '/src/inventario/cabecera_pie_txt';
$data = PostRequest::getDataFromUrl($url_backend);

$cabecera = $data['cabecera'];
$cabeceraB = $data['cabeceraB'];
$firma = $data['firma'];
$pie = $data['pie'];

$oHash = new HashFront();
$oHash->setCamposForm('cabecera!cabeceraB!firma!pie');

$a_campos = [
    'oPosicion' => $oPosicion,
    'cabecera' => $cabecera,
    'cabeceraB' => $cabeceraB,
    'firma' => $firma,
    'pie' => $pie,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('cabecera_pie_txt.phtml', $a_campos);