<?php


/**
 * Endpoint backend: elimina el sacd ({id_activ, id_cargo}) de una
 * actividad y la asistencia asociada. Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'sacd_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ'),
    'id_cargo' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_cargo'),
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom'),
];

/** @var SacdEliminar $useCase */
$useCase = DependencyResolver::get(SacdEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
