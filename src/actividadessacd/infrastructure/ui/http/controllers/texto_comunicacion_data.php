<?php
/**
 * Endpoint backend: devuelve el texto de comunicacion (`clave`, `idioma`).
 */

use src\actividadessacd\application\TextoComunicacionData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'clave' => input_string($_POST, 'clave'),
    'idioma' => input_string($_POST, 'idioma'),
];

/** @var TextoComunicacionData $useCase */
$useCase = DependencyResolver::get(TextoComunicacionData::class);
ContestarJson::enviar('', $useCase->execute($input));
