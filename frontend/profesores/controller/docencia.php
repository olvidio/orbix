<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\profesores\helpers\ProfesoresPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$url_backend = '/src/profesores/docencia';
$data = PostRequest::getDataFromUrl($url_backend);
$tabla = ProfesoresPayload::listaTablaFromPayload($data);

$oTabla = new Lista();
$oTabla->setId_tabla($tabla['id_tabla']);
$oTabla->setCabeceras($tabla['a_cabeceras']);
$oTabla->setDatos($tabla['a_valores']);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('docencia.phtml', $a_campos);
