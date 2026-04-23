<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$url_backend = '/src/profesores/profesor_asignatura_que';
$data = PostRequest::getDataFromUrl($url_backend);
$aOpciones = $data['aOpciones'] ?? [];

$oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
$oDesplAsignaturas->setNombre('id_asignatura');
$oDesplAsignaturas->setAction("fnjs_profes()");

$oHash = new Hash();
$oHash->setUrl('frontend/profesores/controller/profesor_asignatura_ajax.php');
$oHash->setCamposForm('id_asignatura');

$a_campos = [
    'oDesplAsignaturas' => $oDesplAsignaturas,
    'h' => $oHash->linkSinValParams(),
    'url_ajax' => ConfigGlobal::getWeb() . '/frontend/profesores/controller/profesor_asignatura_ajax.php',
];

$oView = new ViewNewPhtml('frontend\profesores\controller');
$oView->renderizar('profesor_asignatura_que.phtml', $a_campos);
