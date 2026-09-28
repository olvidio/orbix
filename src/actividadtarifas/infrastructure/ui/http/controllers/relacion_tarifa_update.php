<?php


/**
 * Endpoint backend: crea o actualiza una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_update'),
        'relacion_tarifa_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$idItemCtx = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_item');
$input = [
    'id_item' => $idItemCtx !== '' ? $idItemCtx : 'nuevo',
    'id_tarifa' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tarifa'),
    'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_activ'),
];

/** @var RelacionTarifaUpdate $useCase */
$useCase = DependencyResolver::get(RelacionTarifaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
