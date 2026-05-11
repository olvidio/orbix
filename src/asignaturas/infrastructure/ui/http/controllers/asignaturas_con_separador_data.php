<?php

use src\asignaturas\application\AsignaturasConSeparadorOpcionesData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $op = ($_POST['op_genericas'] ?? '1') === '0' ? false : true;
    $data = AsignaturasConSeparadorOpcionesData::execute($op);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
