<?php


/**
 * Endpoint backend: guarda las peticiones de una persona+tipo
 * (borra las anteriores y crea las nuevas en orden).
 */

use src\actividadplazas\application\PeticionesGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'peticiones_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom'),
    'sactividad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'sactividad'),
    'actividades' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'actividades'),
];

/** @var PeticionesGuardar $useCase */
$useCase = DependencyResolver::get(PeticionesGuardar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
