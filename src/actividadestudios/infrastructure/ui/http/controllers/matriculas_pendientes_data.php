<?php

use src\actividadestudios\application\MatriculasPendientesData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = MatriculasPendientesData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
