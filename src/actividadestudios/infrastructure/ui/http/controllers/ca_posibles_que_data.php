<?php

use src\actividadestudios\application\CaPosiblesQueData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    /** @var CaPosiblesQueData $useCase */
    $useCase = DependencyResolver::get(CaPosiblesQueData::class);
    $data = $useCase->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
