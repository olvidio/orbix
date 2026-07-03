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

$input = [
    'que' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'que'),
    'poblacion' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'poblacion'),
    'pais' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pais'),
    'region' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'region'),
    'dl' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl'),
];

/** @var CartasPresentacionListaData $useCase */
$useCase = DependencyResolver::get(CartasPresentacionListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
