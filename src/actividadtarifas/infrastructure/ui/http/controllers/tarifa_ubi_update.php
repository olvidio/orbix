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
$ctxRaw = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_update');
try {
    $ctx = HashB::open($ctxRaw, 'tarifa_ubi_update');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item'),
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'year'),
    'id_tarifa' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tarifa'),
    'id_serie' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_serie'),
    'cantidad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'cantidad'),
    'observ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'observ'),
];

/** @var TarifaUbiUpdate $useCase */
$useCase = DependencyResolver::get(TarifaUbiUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
