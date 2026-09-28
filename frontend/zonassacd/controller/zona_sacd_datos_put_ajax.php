<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qctx_put = (string)filter_input(INPUT_POST, 'ctx_put');

header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::getContent('/src/misas/zona_sacd_datos_put', [
    'ctx_put' => $Qctx_put,
    'propia' => (string)filter_input(INPUT_POST, 'propia'),
    'dw1' => (string)filter_input(INPUT_POST, 'dw1'),
    'dw2' => (string)filter_input(INPUT_POST, 'dw2'),
    'dw3' => (string)filter_input(INPUT_POST, 'dw3'),
    'dw4' => (string)filter_input(INPUT_POST, 'dw4'),
    'dw5' => (string)filter_input(INPUT_POST, 'dw5'),
    'dw6' => (string)filter_input(INPUT_POST, 'dw6'),
    'dw7' => (string)filter_input(INPUT_POST, 'dw7'),
]);
