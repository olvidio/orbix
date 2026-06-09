<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$url_backend = '/src/profesores/congresos';
$data = PostRequest::getDataFromUrl($url_backend);

$oTabla = new Lista();
$oTabla->setId_tabla($data['id_tabla']);
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones([]);
$oTabla->setDatos($data['a_valores']);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('congresos.phtml', $a_campos);
