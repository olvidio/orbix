<?php


/**
 * Endpoint backend: reordena sacd encargados (+/- prioridad). Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdReordenar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_reordenar'),
        'sacd_reordenar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ'),
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom'),
    'num_orden' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'num_orden'),
];

/** @var SacdReordenar $useCase */
$useCase = DependencyResolver::get(SacdReordenar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
