<?php

use src\notas\application\AsignaturasPendientesResumenData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(AsignaturasPendientesResumenData::class))->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
