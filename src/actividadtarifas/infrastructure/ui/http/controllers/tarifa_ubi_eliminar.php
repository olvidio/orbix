<?php


/**
 * Endpoint backend: elimina una `TarifaUbi`.
 *
 * Autorización via cápsula `HashB` en `ctx_eliminar`.
 */

use src\actividadtarifas\application\TarifaUbiEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
$ctxRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_eliminar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item'),
];

/** @var TarifaUbiEliminar $useCase */
$useCase = DependencyResolver::get(TarifaUbiEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
