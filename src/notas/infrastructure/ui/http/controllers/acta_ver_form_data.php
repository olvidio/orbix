<?php

use src\notas\application\ActaVerFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = (DependencyResolver::get(ActaVerFormData::class))->execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
