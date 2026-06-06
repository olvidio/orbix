<?php

use src\actividadestudios\application\MatriculasPendientesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    /** @var MatriculasPendientesData $useCase */
    $useCase = DependencyResolver::get(MatriculasPendientesData::class);
    $data = $useCase->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
