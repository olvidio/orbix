<?php

/**
 * Endpoint JSON: datos para el formulario `stgr_cambio.phtml`.
 */

use src\personas\application\StgrCambioData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var StgrCambioData $useCase */
$useCase = DependencyResolver::get(StgrCambioData::class);
$result = $useCase->execute($_POST);

if (!empty($result['error'])) {
    ContestarJson::enviar((string)$result['error']);
    return;
}

ContestarJson::enviar('', $result);
