<?php

use src\actividadestudios\application\ActaNotasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$error = '';
$data = [];
try {
    $idActiv = input_int($_POST, 'id_activ');
    $idAsignatura = input_int($_POST, 'id_asignatura');
    /** @var ActaNotasData $useCase */
    $useCase = DependencyResolver::get(ActaNotasData::class);
    $data = $useCase->execute(['id_activ' => $idActiv, 'id_asignatura' => $idAsignatura]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
