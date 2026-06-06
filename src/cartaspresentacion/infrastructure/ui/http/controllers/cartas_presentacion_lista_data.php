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
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$input = [
    'que' => input_string($_POST, 'que'),
    'poblacion' => input_string($_POST, 'poblacion'),
    'pais' => input_string($_POST, 'pais'),
    'region' => input_string($_POST, 'region'),
    'dl' => input_string($_POST, 'dl'),
];

/** @var CartasPresentacionListaData $useCase */
$useCase = DependencyResolver::get(CartasPresentacionListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
