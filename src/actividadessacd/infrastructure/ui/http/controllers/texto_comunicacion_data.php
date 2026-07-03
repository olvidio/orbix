<?php


/**
 * Endpoint backend: devuelve el texto de comunicacion (`clave`, `idioma`).
 */

use src\actividadessacd\application\TextoComunicacionData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'clave' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'clave'),
    'idioma' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'idioma'),
];

/** @var TextoComunicacionData $useCase */
$useCase = DependencyResolver::get(TextoComunicacionData::class);
ContestarJson::enviar('', $useCase->execute($input));
