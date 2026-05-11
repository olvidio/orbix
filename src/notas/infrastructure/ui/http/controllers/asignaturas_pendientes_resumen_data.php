<?php

use src\notas\application\AsignaturasPendientesResumenData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = AsignaturasPendientesResumenData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
