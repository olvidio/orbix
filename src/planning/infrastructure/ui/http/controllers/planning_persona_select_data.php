<?php

use src\shared\web\ContestarJson;
use src\planning\application\PlanningPersonaSelectData;

$error = '';
$data = [];
try {
    $personas = PlanningPersonaSelectData::execute($_POST);
    $data = ['personas' => $personas];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
