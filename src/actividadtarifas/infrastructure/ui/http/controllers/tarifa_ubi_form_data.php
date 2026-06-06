<?php
/**
 * Endpoint backend: datos del form modificar/nuevo `TarifaUbi`.
 */

use src\actividadtarifas\application\TarifaUbiFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_item' => input_string($_POST, 'id_item'),
    'id_ubi' => input_int($_POST, 'id_ubi'),
    'year' => input_int($_POST, 'year'),
    'letra' => input_string($_POST, 'letra'),
];

/** @var TarifaUbiFormData $useCase */
$useCase = DependencyResolver::get(TarifaUbiFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
