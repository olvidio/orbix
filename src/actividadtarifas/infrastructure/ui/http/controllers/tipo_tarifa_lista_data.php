<?php
/**
 * Endpoint backend: listado del catalogo de tipos de tarifa.
 */

use src\actividadtarifas\application\TipoTarifaListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoTarifaListaData $useCase */
$useCase = DependencyResolver::get(TipoTarifaListaData::class);
ContestarJson::enviar('', $useCase->execute());
