<?php

use src\actividadessacd\application\ComSacdActivPeriodoPageData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

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
