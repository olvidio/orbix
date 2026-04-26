<?php

use src\notas\application\AsignaturasPendientesResumenData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = AsignaturasPendientesResumenData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
