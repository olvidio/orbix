<?php

use src\actividadestudios\application\CaPosiblesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    /** @var CaPosiblesData $useCase */
    $useCase = DependencyResolver::get(CaPosiblesData::class);
    $data = $useCase->execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
