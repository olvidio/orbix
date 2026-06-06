<?php
/**
 * Endpoint backend: listado de relaciones tarifa ↔ tipo actividad.
 */

use src\actividadtarifas\application\RelacionTarifaListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var RelacionTarifaListaData $useCase */
$useCase = DependencyResolver::get(RelacionTarifaListaData::class);
ContestarJson::enviar('', $useCase->execute());
