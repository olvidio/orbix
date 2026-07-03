<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\profesores\helpers\ProfesoresPayload;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_asignatura = (int)filter_input(INPUT_POST, 'id_asignatura');

$url_backend = '/src/profesores/profesor_asignatura_ajax';
$data = PostRequest::getDataFromUrl($url_backend, ['id_asignatura' => $Qid_asignatura]);
$tabla = ProfesoresPayload::listaTablaFromPayload($data);

$oTabla = new Lista();
$oTabla->setId_tabla($tabla['id_tabla']);
$oTabla->setCabeceras($tabla['a_cabeceras']);
$oTabla->setBotones($tabla['a_botones']);
$oTabla->setDatos($tabla['a_valores']);

AjaxJsonSupport::html($oTabla->mostrar_tabla());
