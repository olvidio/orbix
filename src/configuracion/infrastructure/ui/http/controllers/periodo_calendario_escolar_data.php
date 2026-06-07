<?php

use src\configuracion\application\PeriodoCalendarioEscolarData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PeriodoCalendarioEscolarData $useCase */
$useCase = DependencyResolver::get(PeriodoCalendarioEscolarData::class);

$error = '';
$data = [];
try {
    $data = $useCase->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
