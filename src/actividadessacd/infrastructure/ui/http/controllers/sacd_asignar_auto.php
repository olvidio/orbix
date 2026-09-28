<?php


/**
 * Endpoint backend: auto-asignacion masiva del sacd titular del centro
 * encargado a actividades sr/sg sin sacd. Responde JSON
 * `{success, mensaje, data: {asignadas, sin_asignar}}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdAsignarAuto;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_asignar_auto'),
        'sacd_asignar_auto'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'f_ini_iso' => \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'f_ini_iso'),
];

/** @var SacdAsignarAuto $useCase */
$useCase = DependencyResolver::get(SacdAsignarAuto::class);
ContestarJson::enviar('', $useCase->execute($input));
