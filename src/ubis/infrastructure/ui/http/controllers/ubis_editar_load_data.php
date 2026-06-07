<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\application\UbisEditarLoadData;

$error = '';
$data = [];
try {
    $data = DependencyResolver::get(UbisEditarLoadData::class)->execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
