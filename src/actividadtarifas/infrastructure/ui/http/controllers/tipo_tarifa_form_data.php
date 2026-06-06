<?php
/**
 * Endpoint backend: datos del form modificar/nuevo `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_tarifa' => input_string($_POST, 'id_tarifa'),
];

/** @var TipoTarifaFormData $useCase */
$useCase = DependencyResolver::get(TipoTarifaFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
