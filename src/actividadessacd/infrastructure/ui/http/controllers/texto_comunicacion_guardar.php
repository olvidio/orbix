<?php
/**
 * Endpoint backend: guarda/elimina texto de comunicacion sacd.
 */

use src\actividadessacd\application\TextoComunicacionGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'clave' => input_string($_POST, 'clave'),
    'idioma' => input_string($_POST, 'idioma'),
    'texto' => input_string($_POST, 'texto'),
];

/** @var TextoComunicacionGuardar $useCase */
$useCase = DependencyResolver::get(TextoComunicacionGuardar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
