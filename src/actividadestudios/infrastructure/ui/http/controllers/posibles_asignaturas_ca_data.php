<?php

use src\actividadestudios\application\PosiblesAsignaturasCaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $idActiv = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ');
    $nomActiv = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'nom_activ');
    /** @var PosiblesAsignaturasCaData $useCase */
    $useCase = DependencyResolver::get(PosiblesAsignaturasCaData::class);
    $data = $useCase->execute(['id_activ' => $idActiv, 'nom_activ' => $nomActiv]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
