<?php

/**
 * Dataset para montar CasasQue en {@see frontend/planning/controller/planning_casa_que.php}
 * sin `use src` sobre Role/PauType en frontend.
 */

use src\planning\application\PlanningCasaQueFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    /** @var PlanningCasaQueFormData $useCase */
    $useCase = DependencyResolver::get(PlanningCasaQueFormData::class);
    $data = $useCase->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
