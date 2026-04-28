<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;

require_once("frontend/shared/global_header_front.inc");

$url_backend = '/src/profesores/profesor_asignatura_que';
$data = PostRequest::getDataFromUrl($url_backend);
$aOpciones = $data['aOpciones'] ?? [];

$oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
$oDesplAsignaturas->setNombre('id_asignatura');
$oDesplAsignaturas->setAction("fnjs_profes()");

$oHash = new HashFront();
$oHash->setUrl('frontend/profesores/controller/profesor_asignatura_ajax.php');
$oHash->setCamposForm('id_asignatura');

$a_campos = [
    'oDesplAsignaturas' => $oDesplAsignaturas,
    'h' => $oHash->linkSinValParams(),
    'url_ajax' => AppUrlConfig::getPublicAppBaseUrl() . '/frontend/profesores/controller/profesor_asignatura_ajax.php',
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('profesor_asignatura_que.phtml', $a_campos);
