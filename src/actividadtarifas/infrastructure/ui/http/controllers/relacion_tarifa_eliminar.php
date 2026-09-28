<?php


/**
 * Endpoint backend: elimina una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'relacion_tarifa_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item'),
];

/** @var RelacionTarifaEliminar $useCase */
$useCase = DependencyResolver::get(RelacionTarifaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
