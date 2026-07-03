<?php


/**
 * Endpoint backend: datos del form modificar/nuevo `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_tarifa' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tarifa'),
];

/** @var TipoTarifaFormData $useCase */
$useCase = DependencyResolver::get(TipoTarifaFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
