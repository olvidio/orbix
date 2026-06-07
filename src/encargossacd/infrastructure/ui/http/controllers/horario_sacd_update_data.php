<?php

use src\encargossacd\application\EncargoSacdHorarioUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoSacdHorarioUpdate $useCase */
$useCase = DependencyResolver::get(EncargoSacdHorarioUpdate::class);


$result = $useCase->ejecutar($_POST);
if (isset($result['_error'])) {
    ContestarJson::enviar($result['_error'], []);
    return;
}

ContestarJson::enviar('', ['ok' => true]);
