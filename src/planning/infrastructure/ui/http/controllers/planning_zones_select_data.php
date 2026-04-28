<?php

/**
 * Dataset para {@see frontend/planning/controller/planning_zones_select.php}.
 */

use frontend\shared\web\ContestarJson;
use src\planning\application\PlanningZonesSelectData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    $data = PlanningZonesSelectData::execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
