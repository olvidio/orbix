<?php

use src\actividadestudios\application\ActividadAsignaturaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_nueva'),
        'actividad_asignatura_nueva'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');

/** @var ActividadAsignaturaNueva $useCase */
$useCase = DependencyResolver::get(ActividadAsignaturaNueva::class);
$result = $useCase->execute($_POST);
if ($result['requiere_confirmacion']) {
    ContestarJson::enviar('', [
        'requiere_confirmacion' => true,
        'mensaje' => $result['mensaje'],
    ]);
} else {
    ContestarJson::enviar($result['error'], 'ok');
}
