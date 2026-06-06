<?php
/**
 * Endpoint backend: listado de centros con el estado de su carta de
 * presentacion, en dos variantes (delegacion del usuario o centros
 * extranjeros).
 */

use src\cartaspresentacion\application\CartasPresentacionUbisListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$input = [
    'tipo_lista' => input_string($_POST, 'tipo_lista'),
    'poblacion_sel' => input_string($_POST, 'poblacion_sel'),
];

/** @var CartasPresentacionUbisListaData $useCase */
$useCase = DependencyResolver::get(CartasPresentacionUbisListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
