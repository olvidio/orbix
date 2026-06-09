<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/misas/helpers/misas_support.php';

FrontBootstrap::boot();
$post = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla_origen' => (string)filter_input(INPUT_POST, 'tipo_plantilla_origen'),
    'tipo_plantilla_destino' => (string)filter_input(INPUT_POST, 'tipo_plantilla_destino'),
];

// Endpoint de mutacion: escribe masivamente `EncargoDia`. El frontend no consume
// el payload (el AJAX de `modificar_plantilla.phtml` lanza a continuacion otra
// peticion para repintar la cuadricula), asi que basta con disparar la llamada.
PostRequest::getDataFromUrl('/src/misas/importar_plantilla_data', $post);
