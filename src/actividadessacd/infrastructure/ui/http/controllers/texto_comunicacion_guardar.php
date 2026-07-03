<?php


/**
 * Endpoint backend: guarda/elimina texto de comunicacion sacd.
 */

use src\actividadessacd\application\TextoComunicacionGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'clave' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'clave'),
    'idioma' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'idioma'),
    'texto' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'texto'),
];

/** @var TextoComunicacionGuardar $useCase */
$useCase = DependencyResolver::get(TextoComunicacionGuardar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
