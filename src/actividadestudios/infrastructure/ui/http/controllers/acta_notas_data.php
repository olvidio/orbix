<?php

use src\actividadestudios\application\ActaNotasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ');
    $idAsignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_asignatura');
    /** @var ActaNotasData $useCase */
    $useCase = DependencyResolver::get(ActaNotasData::class);
    $data = $useCase->execute(['id_activ' => $idActiv, 'id_asignatura' => $idAsignatura]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
