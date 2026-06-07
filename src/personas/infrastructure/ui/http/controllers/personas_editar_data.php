<?php

/**
 * Endpoint JSON: datos para la ficha `personas_editar.phtml`.
 */

use src\personas\application\PersonasEditarData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PersonasEditarData $useCase */
$useCase = DependencyResolver::get(PersonasEditarData::class);
$result = $useCase->execute($_POST);

$errorVal = $result['error'] ?? '';
if (is_string($errorVal) && $errorVal !== '') {
    ContestarJson::enviar($errorVal);
    return;
}

ContestarJson::enviar('', $result);
