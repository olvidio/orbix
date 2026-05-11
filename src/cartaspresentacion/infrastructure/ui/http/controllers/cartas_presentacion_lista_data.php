<?php
/**
 * Endpoint backend: listado agrupado de cartas de presentacion (modo
 * `lista_dl`, `lista_todo` o `get` con filtros).
 *
 * Devuelve HTML ya montado en los campos `html_lista` y `html_errores`
 * (ver `CartasPresentacionListaData`) — el frontend los imprime sin
 * reformatear.
 */

use src\cartaspresentacion\application\CartasPresentacionListaData;
use src\shared\web\ContestarJson;

$input = [
    'que' => (string)filter_input(INPUT_POST, 'que'),
    'poblacion' => (string)filter_input(INPUT_POST, 'poblacion'),
    'pais' => (string)filter_input(INPUT_POST, 'pais'),
    'region' => (string)filter_input(INPUT_POST, 'region'),
    'dl' => (string)filter_input(INPUT_POST, 'dl'),
];
$data = CartasPresentacionListaData::execute($input);
ContestarJson::enviar('', $data);
