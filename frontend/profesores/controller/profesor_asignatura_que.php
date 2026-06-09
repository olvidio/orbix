<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/profesores_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$url_backend = '/src/profesores/profesor_asignatura_que';
$data = PostRequest::getDataFromUrl($url_backend);
$aOpciones = notas_desplegable_opciones($data['aOpciones'] ?? []);

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
