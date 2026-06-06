<?php
/**
 * Endpoint backend: listado de `GrupoCasa` (relaciones padre ↔ hijo).
 */

use src\casas\application\GrupoCasaListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var GrupoCasaListaData $useCase */
$useCase = DependencyResolver::get(GrupoCasaListaData::class);
ContestarJson::enviar('', $useCase->execute());
