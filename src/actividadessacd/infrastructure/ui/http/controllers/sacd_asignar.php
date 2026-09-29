<?php


/**
 * Endpoint backend: asigna un sacd a una actividad (y, si es sv, tambien
 * crea la asistencia). Responde JSON `{success, mensaje, data}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_asignar'),
        'sacd_asignar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ'),
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom'),
];

/** @var SacdAsignar $useCase */
$useCase = DependencyResolver::get(SacdAsignar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
