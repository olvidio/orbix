<?php

/**
 * Endpoint backend: cápsula HashB acción-only para incorporar peticiones.
 */

use src\actividadplazas\application\PeticionesIncorporarData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PeticionesIncorporarData $useCase */
$useCase = DependencyResolver::get(PeticionesIncorporarData::class);
ContestarJson::enviar('', $useCase->execute());
