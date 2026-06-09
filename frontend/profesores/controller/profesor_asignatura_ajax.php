<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/profesores_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_asignatura = (int)filter_input(INPUT_POST, 'id_asignatura');

$url_backend = '/src/profesores/profesor_asignatura_ajax';
$data = PostRequest::getDataFromUrl($url_backend, ['id_asignatura' => $Qid_asignatura]);
$tabla = profesores_lista_tabla_from_payload($data);

$oTabla = new Lista();
$oTabla->setId_tabla($tabla['id_tabla']);
$oTabla->setCabeceras($tabla['a_cabeceras']);
$oTabla->setBotones($tabla['a_botones']);
$oTabla->setDatos($tabla['a_valores']);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('profesor_asignatura_ajax.phtml', $a_campos);
