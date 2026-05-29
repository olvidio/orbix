<?php

use frontend\misas\support\CuadriculaZonaRenderer;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$post = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'empiezamin' => (string)(filter_input(INPUT_POST, 'empiezamin') ?? ''),
    'empiezamax' => (string)(filter_input(INPUT_POST, 'empiezamax') ?? ''),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
];

$data = PostRequest::getDataFromUrl('/src/misas/ver_misas_zona_data', $post);

CuadriculaZonaRenderer::renderVer(
    $data,
    $post,
    'frontend/misas/controller/ver_misas_zona.php',
    'id_zona!seleccion!empiezamin!empiezamax!fila!columna',
    [
        'tipo_plantilla' => 'p',
        'orden' => 'prioridad',
        'periodo' => '',
        'fila' => 0,
        'columna' => 0,
    ],
);
