<?php
/**
 * Endpoint backend: elimina una `TarifaUbi`.
 *
 * Autorización: espera un campo POST `ctx_eliminar` con una cápsula
 * `HashB` firmada para la acción `tarifa_ubi_eliminar`. El `id_item`
 * a eliminar se toma del contexto firmado, no del body.
 */

use src\actividadtarifas\application\TarifaUbiEliminar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use frontend\shared\web\ContestarJson;

$ctxRaw = (string)filter_input(INPUT_POST, 'ctx_eliminar');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_eliminar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => (int)($ctx['id_item'] ?? 0),
];

$error = TarifaUbiEliminar::execute($input);
ContestarJson::enviar($error, 'ok');
