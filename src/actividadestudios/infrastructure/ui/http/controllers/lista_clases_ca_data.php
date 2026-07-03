<?php

use src\actividadestudios\application\ListaClasesCaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ');
    /** @var ListaClasesCaData $useCase */
    $useCase = DependencyResolver::get(ListaClasesCaData::class);
    $data = $useCase->execute(['id_activ' => $idActiv]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
