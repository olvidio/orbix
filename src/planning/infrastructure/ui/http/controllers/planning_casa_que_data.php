<?php

/**
 * Dataset para montar CasasQue en {@see frontend/planning/controller/planning_casa_que.php}
 * sin `use src` sobre Role/PauType en frontend.
 */

use src\shared\web\ContestarJson;
use src\planning\application\PlanningCasaQueFormData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    $data = PlanningCasaQueFormData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
