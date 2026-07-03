<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: listado de centros con el estado de su carta de
 * presentacion, en dos variantes (delegacion del usuario o centros
 * extranjeros).
 */

use src\cartaspresentacion\application\CartasPresentacionUbisListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'tipo_lista' => FuncTablasSupport::inputString($_POST, 'tipo_lista'),
    'poblacion_sel' => FuncTablasSupport::inputString($_POST, 'poblacion_sel'),
];

/** @var CartasPresentacionUbisListaData $useCase */
$useCase = DependencyResolver::get(CartasPresentacionUbisListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
