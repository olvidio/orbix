<?php

use src\actividadestudios\application\MatriculaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var MatriculaNueva $useCase */
$useCase = DependencyResolver::get(MatriculaNueva::class);
$result = $useCase->execute($_POST);
if ($result['requiere_confirmacion']) {
    ContestarJson::enviar('', [
        'requiere_confirmacion' => true,
        'mensaje' => $result['mensaje'],
    ]);
} else {
    ContestarJson::enviar($result['error'], 'ok');
}
