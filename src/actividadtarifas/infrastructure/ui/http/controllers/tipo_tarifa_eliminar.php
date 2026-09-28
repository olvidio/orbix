<?php


/**
 * Endpoint backend: elimina un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'tipo_tarifa_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_tarifa' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tarifa'),
];

/** @var TipoTarifaEliminar $useCase */
$useCase = DependencyResolver::get(TipoTarifaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
