<?php

/**
 * Dataset para {@see frontend/planning/controller/planning_casa_ver.php}
 * (`ActividadesPorCasasService` + `CasaPeriodosForPlanning`).
 */

use src\planning\application\PlanningCasaVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    /** @var PlanningCasaVerData $useCase */
    $useCase = DependencyResolver::get(PlanningCasaVerData::class);
    $data = $useCase->execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
