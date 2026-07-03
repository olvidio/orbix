<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: listado de `TarifaUbi` por `id_ubi` + `year`.
 */

use src\actividadtarifas\application\TarifaUbiListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_ubi' => FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'year' => FuncTablasSupport::inputInt($_POST, 'year'),
];

/** @var TarifaUbiListaData $useCase */
$useCase = DependencyResolver::get(TarifaUbiListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
