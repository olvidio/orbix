<?php

use src\asignaturas\application\AsignaturasMapData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = AsignaturasMapData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
