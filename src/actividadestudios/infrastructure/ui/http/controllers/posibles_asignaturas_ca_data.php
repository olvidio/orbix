<?php

use src\actividadestudios\application\PosiblesAsignaturasCaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$error = '';
$data = [];
try {
    $idActiv = input_int($_POST, 'id_activ');
    $nomActiv = input_string($_POST, 'nom_activ');
    /** @var PosiblesAsignaturasCaData $useCase */
    $useCase = DependencyResolver::get(PosiblesAsignaturasCaData::class);
    $data = $useCase->execute(['id_activ' => $idActiv, 'nom_activ' => $nomActiv]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
