<?php

use src\shared\domain\helpers\FuncTablasSupport;

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
$ctxRaw = FuncTablasSupport::inputString($_POST, 'ctx_update');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_update');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => FuncTablasSupport::inputInt($ctx, 'id_item'),
    'id_ubi' => FuncTablasSupport::inputInt($ctx, 'id_ubi'),
    'year' => FuncTablasSupport::inputInt($ctx, 'year'),
    'id_tarifa' => FuncTablasSupport::inputInt($_POST, 'id_tarifa'),
    'id_serie' => FuncTablasSupport::inputInt($_POST, 'id_serie'),
    'cantidad' => FuncTablasSupport::inputString($_POST, 'cantidad'),
    'observ' => FuncTablasSupport::inputString($_POST, 'observ'),
];

/** @var TarifaUbiUpdate $useCase */
$useCase = DependencyResolver::get(TarifaUbiUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
