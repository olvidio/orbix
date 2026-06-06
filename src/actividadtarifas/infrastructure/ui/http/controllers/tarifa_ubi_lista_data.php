<?php
/**
 * Endpoint backend: listado de `TarifaUbi` por `id_ubi` + `year`.
 */

use src\actividadtarifas\application\TarifaUbiListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$input = [
    'id_ubi' => input_int($_POST, 'id_ubi'),
    'year' => input_int($_POST, 'year'),
];

/** @var TarifaUbiListaData $useCase */
$useCase = DependencyResolver::get(TarifaUbiListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
