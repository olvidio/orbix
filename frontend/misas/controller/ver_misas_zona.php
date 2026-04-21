<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$post = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'empiezamin' => (string)(filter_input(INPUT_POST, 'empiezamin') ?? ''),
    'empiezamax' => (string)(filter_input(INPUT_POST, 'empiezamax') ?? ''),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
];

$data = PostRequest::getDataFromUrl('/src/misas/ver_misas_zona_data', $post);

$columns_cuadricula = $data['columns_cuadricula'] ?? '[]';
$json_data_cuadricula = $data['data_cuadricula'] ?? [];

$url_cuadricula_update = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/cuadricula_update';
$oHashUpd = new Hash();
$oHashUpd->setUrl($url_cuadricula_update);
$oHashUpd->setCamposForm('dia!id_enc!key!observ!tend!tstart!uuid_item!tipo_plantilla!id_zona');
$h_cuadricula_update = $oHashUpd->linkSinVal();

$url_desplegable_sacd = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/desplegable_sacd';
$oHashDs = new Hash();
$oHashDs->setUrl($url_desplegable_sacd);
$oHashDs->setCamposForm('id_zona!id_sacd!seleccion!dia');
$h_desplegable_sacd = $oHashDs->linkSinVal();

$url_self = 'frontend/misas/controller/ver_misas_zona.php';
$oHashSelf = new Hash();
$oHashSelf->setUrl($url_self);
$oHashSelf->setCamposForm('id_zona!seleccion!empiezamin!empiezamax!fila!columna');
$h_ver = $oHashSelf->linkSinVal();

$a_campos = [
    'columns_cuadricula' => $columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'url_desplegable_sacd' => $url_desplegable_sacd,
    'h_desplegable_sacd' => $h_desplegable_sacd,
    'url_ver_cuadricula_zona' => $url_self,
    'h_ver_cuadricula_zona' => $h_ver,
    'id_zona' => (int)($data['id_zona'] ?? $post['id_zona']),
    'tipo_plantilla' => 'p',
    'orden' => 'prioridad',
    'seleccion' => (int)($data['seleccion'] ?? $post['seleccion']),
    'periodo' => '',
    'empieza_min' => (string)($data['empieza_min'] ?? $post['empiezamin']),
    'empieza_max' => (string)($data['empieza_max'] ?? $post['empiezamax']),
    'fila' => 0,
    'columna' => 0,
    'h_cuadricula_update' => $h_cuadricula_update,
    'url_cuadricula_update' => $url_cuadricula_update,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_cuadricula_zona.phtml', $a_campos);
