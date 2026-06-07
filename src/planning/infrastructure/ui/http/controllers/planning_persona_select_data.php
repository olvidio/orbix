<?php

use src\planning\application\PlanningPersonaSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    /** @var PlanningPersonaSelectData $useCase */
    $useCase = DependencyResolver::get(PlanningPersonaSelectData::class);
    $personas = $useCase->execute($_POST);
    $data = ['personas' => $personas];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
