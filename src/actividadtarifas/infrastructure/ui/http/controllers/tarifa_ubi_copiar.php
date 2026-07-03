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
$ctxRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_copiar');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_copiar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'year'),
];

/** @var TarifaUbiCopiar $useCase */
$useCase = DependencyResolver::get(TarifaUbiCopiar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
