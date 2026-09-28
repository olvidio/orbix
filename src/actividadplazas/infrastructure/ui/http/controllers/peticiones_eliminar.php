<?php


/**
 * Endpoint backend: elimina todas las peticiones de una
 * persona+tipo.
 */

use src\actividadplazas\application\PeticionesEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'peticiones_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom'),
    'sactividad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'sactividad'),
];

/** @var PeticionesEliminar $useCase */
$useCase = DependencyResolver::get(PeticionesEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
