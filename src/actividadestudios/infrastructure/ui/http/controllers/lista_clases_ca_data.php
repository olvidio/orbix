<?php

use src\actividadestudios\application\ListaClasesCaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$error = '';
$data = [];
try {
    $idActiv = input_int($_POST, 'id_activ');
    /** @var ListaClasesCaData $useCase */
    $useCase = DependencyResolver::get(ListaClasesCaData::class);
    $data = $useCase->execute(['id_activ' => $idActiv]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
