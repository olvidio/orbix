<?php


/**
 * Endpoint JSON: elimina un `CambioUsuarioObjetoPref`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'cambio_usuario_objeto_pref_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item_usuario_objeto' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item_usuario_objeto'),
];

/** @var CambioUsuarioObjetoPrefEliminar $useCase */
$useCase = DependencyResolver::get(CambioUsuarioObjetoPrefEliminar::class);
$result = $useCase->execute($input);
$error = (string)$result['error'];

ContestarJson::enviar($error, []);
