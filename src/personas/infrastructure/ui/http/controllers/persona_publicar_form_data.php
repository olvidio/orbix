<?php

/**
 * Endpoint JSON: datos para el formulario de publicar persona.
 */

use src\personas\application\PersonaPublicarFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PersonaPublicarFormData $useCase */
$useCase = DependencyResolver::get(PersonaPublicarFormData::class);
$result = $useCase->execute($_POST);

if (!empty($result['error'])) {
    ContestarJson::enviar((string) $result['error']);
    return;
}

ContestarJson::enviar('', $result);
