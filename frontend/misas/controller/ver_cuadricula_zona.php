<?php

use frontend\misas\support\CuadriculaZonaRenderer;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$post = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla' => (string)filter_input(INPUT_POST, 'tipo_plantilla'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'orden' => (string)filter_input(INPUT_POST, 'orden'),
    'empiezamin' => (string)(filter_input(INPUT_POST, 'empiezamin') ?? ''),
    'empiezamax' => (string)(filter_input(INPUT_POST, 'empiezamax') ?? ''),
    'fila' => (int)filter_input(INPUT_POST, 'fila'),
    'columna' => (int)filter_input(INPUT_POST, 'columna'),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
];

if ($post['orden'] === '') {
    $post['orden'] = 'desc_enc';
}

$data = PostRequest::getDataFromUrl('/src/misas/ver_cuadricula_zona_data', $post);

CuadriculaZonaRenderer::render(
    $data,
    $post,
    'frontend/misas/controller/ver_cuadricula_zona.php',
    'id_zona!tipo_plantilla!orden!seleccion!periodo!empiezamin!empiezamax!fila!columna',
);
