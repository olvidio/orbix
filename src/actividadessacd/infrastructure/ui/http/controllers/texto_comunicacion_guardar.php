<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: guarda/elimina texto de comunicacion sacd.
 */

use src\actividadessacd\application\TextoComunicacionGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'clave' => FuncTablasSupport::inputString($_POST, 'clave'),
    'idioma' => FuncTablasSupport::inputString($_POST, 'idioma'),
    'texto' => FuncTablasSupport::inputString($_POST, 'texto'),
];

/** @var TextoComunicacionGuardar $useCase */
$useCase = DependencyResolver::get(TextoComunicacionGuardar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
