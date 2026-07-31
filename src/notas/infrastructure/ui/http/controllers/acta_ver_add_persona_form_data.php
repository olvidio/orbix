<?php

/**
 * Endpoint JSON: datos para añadir alumno+nota en acta_ver.
 */

use src\notas\application\ActaVerAddPersonaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActaVerAddPersonaFormData $useCase */
$useCase = DependencyResolver::get(ActaVerAddPersonaFormData::class);
$result = $useCase->execute($_POST);

if (!empty($result['error']) && empty($result['puede_anadir'])) {
    ContestarJson::enviar((string) $result['error']);
    return;
}

ContestarJson::enviar('', $result);
