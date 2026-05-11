<?php
/**
 * Endpoint backend: copiar tarifas del año anterior.
 *
 * Autorización: espera un campo POST `ctx_copiar` con una cápsula
 * `HashB` firmada para la acción `tarifa_ubi_copiar`. `id_ubi` y
 * `year` se toman del contexto firmado.
 *
 * Vease `TarifaUbiCopiar` — accion heredada rota, pendiente de
 * reimplementar.
 */

use src\actividadtarifas\application\TarifaUbiCopiar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$ctxRaw = (string)filter_input(INPUT_POST, 'ctx_copiar');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_copiar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_ubi' => (int)($ctx['id_ubi'] ?? 0),
    'year' => (int)($ctx['year'] ?? 0),
];

$error = TarifaUbiCopiar::execute($input);
ContestarJson::enviar($error, 'ok');
