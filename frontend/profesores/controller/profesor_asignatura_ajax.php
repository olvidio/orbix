<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_asignatura = (int)filter_input(INPUT_POST, 'id_asignatura');

$url_backend = '/src/profesores/profesor_asignatura_ajax';
$data = PostRequest::getDataFromUrl($url_backend, ['id_asignatura' => $Qid_asignatura]);

$oTabla = new Lista();
$oTabla->setId_tabla($data['id_tabla']);
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones($data['a_botones']);
$oTabla->setDatos($data['a_valores']);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('profesor_asignatura_ajax.phtml', $a_campos);
