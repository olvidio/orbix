<?php


/**
 * Endpoint backend: elimina una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'carta_presentacion_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi'),
    'id_direccion' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_direccion'),
];

/** @var CartaPresentacionEliminar $useCase */
$useCase = DependencyResolver::get(CartaPresentacionEliminar::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
