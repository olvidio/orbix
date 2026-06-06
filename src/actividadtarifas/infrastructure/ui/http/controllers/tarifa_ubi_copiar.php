<?php
/**
 * Endpoint backend: copiar tarifas del año anterior.
 *
 * Autorización via cápsula `HashB` en `ctx_copiar`.
 */

use src\actividadtarifas\application\TarifaUbiCopiar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$ctxRaw = input_string($_POST, 'ctx_copiar');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_copiar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_ubi' => input_int($ctx, 'id_ubi'),
    'year' => input_int($ctx, 'year'),
];

/** @var TarifaUbiCopiar $useCase */
$useCase = DependencyResolver::get(TarifaUbiCopiar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
