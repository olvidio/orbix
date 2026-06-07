<?php

use src\notas\application\AsignaturasPendientesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(AsignaturasPendientesData::class))->execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
