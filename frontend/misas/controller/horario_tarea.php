<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
require_once 'frontend/misas/helpers/misas_support.php';

FrontBootstrap::boot();
$Qid_item_h = (int)filter_input(INPUT_POST, 'id_item_h');

$data = PostRequest::getDataFromUrl('/src/misas/horario_tarea_data', [
    'id_item_h' => $Qid_item_h,
]);

$t_start = misas_string($data['t_start'] ?? '');
$t_end = misas_string($data['t_end'] ?? '');

$url_guardar = AppUrlConfig::getApiBaseUrl() . '/src/misas/guardar_horario';
$oHash = new HashFront();
$oHash->setArrayCamposHidden(['id_item_h' => $Qid_item_h]);
$oHash->setUrl($url_guardar);
$oHash->setCamposForm('t_start!t_end');
$param_guardar = $oHash->getParamAjax();

$url_quitar = AppUrlConfig::getApiBaseUrl() . '/src/misas/quitar_horario';
$oHash->setUrl($url_quitar);
$oHash->setCamposForm('id_item');
$param_quitar = $oHash->getParamAjax();

$a_campos = [
    't_start' => $t_start,
    't_end' => $t_end,
    'url_guardar' => $url_guardar,
    'url_quitar' => $url_quitar,
    'param_guardar' => $param_guardar,
    'param_quitar' => $param_quitar,
];

ajax_json_render_phtml('frontend\\misas\\controller', 'horario_tarea.phtml', $a_campos);
