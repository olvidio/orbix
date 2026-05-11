<?php

use src\shared\web\ContestarJson;
use src\planning\application\PlanningZonesQueData;

$error = '';
$data = [];
try {
    $result = PlanningZonesQueData::execute();
    if ($result['error'] !== '') {
        $error = $result['error'];
    } else {
        $data = ['opciones_zonas' => $result['opciones_zonas']];
    }
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
