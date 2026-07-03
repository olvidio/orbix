<?php


/**
 * Endpoint backend: listado de `TarifaUbi` por `id_ubi` + `year`.
 */

use src\actividadtarifas\application\TarifaUbiListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year'),
];

/** @var TarifaUbiListaData $useCase */
$useCase = DependencyResolver::get(TarifaUbiListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
