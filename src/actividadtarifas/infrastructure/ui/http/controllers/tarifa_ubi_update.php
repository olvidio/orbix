<?php
/**
 * Endpoint backend: crea o actualiza una `TarifaUbi`.
 *
 * Autorización via cápsula `HashB` en `ctx_update`.
 */

use src\actividadtarifas\application\TarifaUbiUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$ctxRaw = input_string($_POST, 'ctx_update');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_update');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => input_int($ctx, 'id_item'),
    'id_ubi' => input_int($ctx, 'id_ubi'),
    'year' => input_int($ctx, 'year'),
    'id_tarifa' => input_int($_POST, 'id_tarifa'),
    'id_serie' => input_int($_POST, 'id_serie'),
    'cantidad' => input_string($_POST, 'cantidad'),
    'observ' => input_string($_POST, 'observ'),
];

/** @var TarifaUbiUpdate $useCase */
$useCase = DependencyResolver::get(TarifaUbiUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
