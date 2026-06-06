<?php
/**
 * Endpoint backend: datos del form modificar/nuevo
 * `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_item' => input_string($_POST, 'id_item'),
];

/** @var RelacionTarifaFormData $useCase */
$useCase = DependencyResolver::get(RelacionTarifaFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
