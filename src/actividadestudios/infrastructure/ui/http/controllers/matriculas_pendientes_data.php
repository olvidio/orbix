<?php

use src\actividadestudios\application\MatriculasPendientesData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = MatriculasPendientesData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
