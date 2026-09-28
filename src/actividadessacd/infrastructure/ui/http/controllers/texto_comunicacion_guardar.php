<?php


/**
 * Endpoint backend: guarda/elimina texto de comunicacion sacd.
 */

use src\actividadessacd\application\TextoComunicacionGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'texto_comunicacion_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'clave' => \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'clave'),
    'idioma' => \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'idioma'),
    'texto' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'texto'),
];

/** @var TextoComunicacionGuardar $useCase */
$useCase = DependencyResolver::get(TextoComunicacionGuardar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
