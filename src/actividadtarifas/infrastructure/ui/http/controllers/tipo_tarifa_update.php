<?php


/**
 * Endpoint backend: crea o actualiza un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_update'),
        'tipo_tarifa_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$idTarifaCtx = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_tarifa');
$input = [
    'id_tarifa' => $idTarifaCtx !== '' ? $idTarifaCtx : 'nuevo',
    'letra' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'letra'),
    'modo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'modo'),
    'observ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'observ'),
];

/** @var TipoTarifaUpdate $useCase */
$useCase = DependencyResolver::get(TipoTarifaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
