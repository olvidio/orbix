<?php
/**
 * Endpoint backend: crea o actualiza una `TarifaUbi`.
 *
 * Autorización: espera un campo POST `ctx_update` con una cápsula
 * `HashB` firmada para la acción `tarifa_ubi_update`. El contexto
 * firmado aporta `id_item`, `id_ubi` y `year` de forma inmutable
 * desde la capa frontend. Los campos que el usuario edita en el form
 * (`id_tarifa`, `id_serie`, `cantidad`, `observ`) siguen llegando
 * como POST normales y los valida `TarifaUbiUpdate`.
 *
 * Durante la fase transitoria el navegador puede seguir enviando
 * `id_item`, `id_ubi`, `year` en el body; aquí se **ignoran**: la
 * verdad está en la cápsula.
 */

use src\actividadtarifas\application\TarifaUbiUpdate;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

$ctxRaw = (string)filter_input(INPUT_POST, 'ctx_update');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_update');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => (int)($ctx['id_item'] ?? 0),
    'id_ubi' => (int)($ctx['id_ubi'] ?? 0),
    'year' => (int)($ctx['year'] ?? 0),
    'id_tarifa' => (int)filter_input(INPUT_POST, 'id_tarifa'),
    'id_serie' => (int)filter_input(INPUT_POST, 'id_serie'),
    'cantidad' => (string)filter_input(INPUT_POST, 'cantidad'),
    'observ' => (string)filter_input(INPUT_POST, 'observ'),
];

$error = TarifaUbiUpdate::execute($input);
ContestarJson::enviar($error, 'ok');
