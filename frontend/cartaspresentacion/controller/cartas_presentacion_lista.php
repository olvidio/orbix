<?php
/**
 * Pantalla frontend: listado agrupado de cartas de presentacion.
 *
 * Sucesor de `apps/cartaspresentacion/controller/cartas_presentacion_lista.php`.
 * Delega en `/src/cartaspresentacion/cartas_presentacion_lista_data` y
 * se limita a imprimir el HTML ya montado por el use case.
 *
 * Se llama en tres modos via POST:
 *  - `que=lista_dl`   (menu "lista dl")
 *  - `que=lista_todo` (menu "lista todo")
 *  - `que=get`        (resultado de la pantalla buscar)
 */

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'que' => (string)filter_input(INPUT_POST, 'que'),
    'poblacion' => (string)filter_input(INPUT_POST, 'poblacion'),
    'pais' => (string)filter_input(INPUT_POST, 'pais'),
    'region' => (string)filter_input(INPUT_POST, 'region'),
    'dl' => (string)filter_input(INPUT_POST, 'dl'),
];

$data = PostRequest::getDataFromUrl('/src/cartaspresentacion/cartas_presentacion_lista_data', $campos);
$payload = is_array($data) ? $data : [];

echo (string)($payload['html_lista'] ?? '');
echo (string)($payload['html_errores'] ?? '');
