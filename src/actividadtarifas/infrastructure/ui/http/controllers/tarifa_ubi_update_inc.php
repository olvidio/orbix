<?php
/**
 * Endpoint backend: actualiza en lote las cantidades de varias
 * `TarifaUbi` desde el estudio economico de casa.
 */

use src\actividadtarifas\application\TarifaUbiUpdateInc;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$inc_cantidad = filter_input(INPUT_POST, 'inc_cantidad', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$input = [
    'inc_cantidad' => is_array($inc_cantidad) ? $inc_cantidad : [],
];

/** @var TarifaUbiUpdateInc $useCase */
$useCase = DependencyResolver::get(TarifaUbiUpdateInc::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
