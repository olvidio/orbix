<?php

use src\actividadestudios\application\PlanEstudiosCaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ');
    /** @var PlanEstudiosCaData $useCase */
    $useCase = DependencyResolver::get(PlanEstudiosCaData::class);
    $data = $useCase->execute(['id_activ' => $idActiv]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
