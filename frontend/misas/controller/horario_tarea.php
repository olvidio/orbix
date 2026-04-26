<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$Qid_item_h = (int)filter_input(INPUT_POST, 'id_item_h');

$data = PostRequest::getDataFromUrl('/src/misas/horario_tarea_data', [
    'id_item_h' => $Qid_item_h,
]);

$t_start = (string)($data['t_start'] ?? '');
$t_end = (string)($data['t_end'] ?? '');

$url_guardar = AppUrlConfig::getApiBaseUrl() . '/src/misas/guardar_horario';
$oHash = new Hash();
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

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('horario_tarea.phtml', $a_campos);
