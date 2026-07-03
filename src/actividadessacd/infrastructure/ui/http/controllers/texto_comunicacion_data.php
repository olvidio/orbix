<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: devuelve el texto de comunicacion (`clave`, `idioma`).
 */

use src\actividadessacd\application\TextoComunicacionData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'clave' => FuncTablasSupport::inputString($_POST, 'clave'),
    'idioma' => FuncTablasSupport::inputString($_POST, 'idioma'),
];

/** @var TextoComunicacionData $useCase */
$useCase = DependencyResolver::get(TextoComunicacionData::class);
ContestarJson::enviar('', $useCase->execute($input));
