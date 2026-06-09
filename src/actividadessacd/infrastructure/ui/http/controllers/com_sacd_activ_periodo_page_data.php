<?php

use src\actividadessacd\application\ComSacdActivPeriodoPageData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


$error = '';
$data = [];
try {
    /** @var ComSacdActivPeriodoPageData $useCase */
    $useCase = DependencyResolver::get(ComSacdActivPeriodoPageData::class);
    $data = $useCase->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
