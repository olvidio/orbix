<?php

use src\configuracion\application\PeriodoCalendarioEscolarData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = PeriodoCalendarioEscolarData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
