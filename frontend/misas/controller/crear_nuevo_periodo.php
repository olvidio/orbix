<?php

use frontend\misas\support\CuadriculaZonaRenderer;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/misas/helpers/misas_support.php';

FrontBootstrap::boot();
$post = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla' => (string)filter_input(INPUT_POST, 'tipoplantilla'),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)(filter_input(INPUT_POST, 'empiezamin') ?? ''),
    'empiezamax' => (string)(filter_input(INPUT_POST, 'empiezamax') ?? ''),
    'orden' => (string)(filter_input(INPUT_POST, 'orden') ?? ''),
];

if ($post['orden'] === '') {
    $post['orden'] = 'desc_enc';
}

$data = PostRequest::getDataFromUrl('/src/misas/crear_nuevo_periodo_data', $post);

CuadriculaZonaRenderer::renderModificar(
    $data,
    $post,
    'frontend/misas/controller/ver_cuadricula_zona.php',
    'id_zona!tipo_plantilla!orden!seleccion!periodo!empiezamin!empiezamax!fila!columna',
    [
        'fila' => 0,
        'columna' => 0,
    ],
);
