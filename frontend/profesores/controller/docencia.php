<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$url_backend = '/src/profesores/docencia';
$data = PostRequest::getDataFromUrl($url_backend);

$oTabla = new Lista();
$oTabla->setId_tabla($data['id_tabla']);
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setDatos($data['a_valores']);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('docencia.phtml', $a_campos);
