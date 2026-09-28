<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_item_h = (int)filter_input(INPUT_POST, 'id_item_h');

$data = PostRequest::getDataFromUrl('/src/misas/horario_tarea_data', [
    'id_item_h' => $Qid_item_h,
]);

$t_start = \frontend\shared\helpers\PayloadCoercion::string($data['t_start'] ?? '');
$t_end = \frontend\shared\helpers\PayloadCoercion::string($data['t_end'] ?? '');
$ctx_guardar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_guardar'] ?? '');
$ctx_quitar = \frontend\shared\helpers\PayloadCoercion::string($data['ctx_quitar'] ?? '');

$url_guardar = AppUrlConfig::srcBrowserUrl('/src/misas/guardar_horario');
$oHashGuardar = new HashF();
$oHashGuardar->setArrayCamposHidden([
    'id_item_h' => $Qid_item_h,
    'ctx_guardar' => $ctx_guardar,
]);
$oHashGuardar->setUrl($url_guardar);
$oHashGuardar->setCamposForm('t_start!t_end');
$param_guardar = $oHashGuardar->getParamAjax();

$url_quitar = AppUrlConfig::srcBrowserUrl('/src/misas/quitar_horario');
$oHashQuitar = new HashF();
$oHashQuitar->setArrayCamposHidden([
    'id_item' => $Qid_item_h,
    'ctx_quitar' => $ctx_quitar,
]);
$oHashQuitar->setUrl($url_quitar);
$oHashQuitar->setCamposForm('id_item');
$param_quitar = $oHashQuitar->getParamAjax();

$a_campos = [
    't_start' => $t_start,
    't_end' => $t_end,
    'url_guardar' => $url_guardar,
    'url_quitar' => $url_quitar,
    'param_guardar' => $param_guardar,
    'param_quitar' => $param_quitar,
];

AjaxJsonSupport::renderPhtml('frontend\\misas\\controller', 'horario_tarea.phtml', $a_campos);
