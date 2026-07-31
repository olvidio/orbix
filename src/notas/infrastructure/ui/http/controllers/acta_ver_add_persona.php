<?php

/**
 * Endpoint JSON: añade alumno con nota al dossier desde acta_ver.
 */

use src\notas\application\ActaVerAddPersona;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActaVerAddPersona $useCase */
$useCase = DependencyResolver::get(ActaVerAddPersona::class);
$result = $useCase->execute($_POST);

$error = ($result['success'] ?? false) ? '' : (string) ($result['mensaje'] ?? _('error'));
ContestarJson::enviar($error, [
    'mensaje' => (string) ($result['mensaje'] ?? ''),
]);
