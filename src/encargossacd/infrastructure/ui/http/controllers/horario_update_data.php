<?php

use src\encargossacd\application\EncargoHorarioUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoHorarioUpdate $useCase */
$useCase = DependencyResolver::get(EncargoHorarioUpdate::class);


$result = $useCase->ejecutar($_POST);
if (isset($result['_error'])) {
    ContestarJson::enviar($result['_error'], []);
    return;
}

ContestarJson::enviar('', ['ok' => true]);
