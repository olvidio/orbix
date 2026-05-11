<?php

/**
 * Dataset para {@see frontend/planning/controller/planning_casa_ver.php}
 * (`ActividadesPorCasasService` + `CasaPeriodosForPlanning`).
 */

use src\shared\web\ContestarJson;
use src\planning\application\PlanningCasaVerData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    $data = PlanningCasaVerData::execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
