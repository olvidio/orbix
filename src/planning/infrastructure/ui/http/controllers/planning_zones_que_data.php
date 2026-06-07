<?php

use src\planning\application\PlanningZonesQueData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    /** @var PlanningZonesQueData $useCase */
    $useCase = DependencyResolver::get(PlanningZonesQueData::class);
    $result = $useCase->execute();
    if ($result['error'] !== '') {
        $error = $result['error'];
    } else {
        $data = ['opciones_zonas' => $result['opciones_zonas']];
    }
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
