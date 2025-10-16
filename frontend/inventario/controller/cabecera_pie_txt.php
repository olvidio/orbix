<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$url_backend = '/src/inventario/infrastructure/controllers/cabecera_pie_txt.php';
$data = PostRequest::getDataFromUrl($url_backend);

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

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('cabecera_pie_txt.phtml', $a_campos);