<?php

use src\actividadestudios\application\ActividadAsignaturaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

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
