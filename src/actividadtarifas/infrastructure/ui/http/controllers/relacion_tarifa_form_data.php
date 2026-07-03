<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: datos del form modificar/nuevo
 * `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_item' => FuncTablasSupport::inputString($_POST, 'id_item'),
];

/** @var RelacionTarifaFormData $useCase */
$useCase = DependencyResolver::get(RelacionTarifaFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
