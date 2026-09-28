<?php

use src\actividadestudios\application\ActividadAsignaturaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_eliminar'),
        'actividad_asignatura_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$_POST['id_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ');
$_POST['id_asignatura'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_asignatura');
unset($_POST['sel']);

/** @var ActividadAsignaturaEliminar $useCase */
$useCase = DependencyResolver::get(ActividadAsignaturaEliminar::class);
$result = $useCase->execute($_POST);
if ($result['requiere_confirmacion']) {
    ContestarJson::enviar('', [
        'requiere_confirmacion' => true,
        'mensaje' => $result['mensaje'],
    ]);
} else {
    ContestarJson::enviar($result['error'], 'ok');
}
