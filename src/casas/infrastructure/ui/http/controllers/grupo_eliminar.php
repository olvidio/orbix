<?php


/**
 * Endpoint backend: elimina un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'grupo_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item'),
];

/** @var GrupoCasaEliminar $useCase */
$useCase = DependencyResolver::get(GrupoCasaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
