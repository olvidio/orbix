<?php

/**
 * Endpoint JSON: datos para el formulario de traslado.
 */

use src\personas\application\TrasladoFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TrasladoFormData $useCase */
$useCase = DependencyResolver::get(TrasladoFormData::class);
$result = $useCase->execute($_POST);

$errorVal = $result['error'] ?? '';
if (is_string($errorVal) && $errorVal !== '') {
    ContestarJson::enviar($errorVal);
    return;
}

ContestarJson::enviar('', $result);
