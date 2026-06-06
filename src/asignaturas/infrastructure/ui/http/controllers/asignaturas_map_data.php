<?php

use src\asignaturas\application\AsignaturasMapData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    /** @var AsignaturasMapData $useCase */
    $useCase = DependencyResolver::get(AsignaturasMapData::class);
    $data = $useCase->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
