<?php

/**
 * Dataset para {@see frontend/planning/controller/planning_zones_select.php}.
 */

use src\planning\application\PlanningZonesSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


$error = '';
$data = [];

try {
    /** @var PlanningZonesSelectData $useCase */
    $useCase = DependencyResolver::get(PlanningZonesSelectData::class);
    $data = $useCase->execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
