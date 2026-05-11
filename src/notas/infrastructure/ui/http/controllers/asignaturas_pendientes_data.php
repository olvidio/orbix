<?php

use src\notas\application\AsignaturasPendientesData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = AsignaturasPendientesData::execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
