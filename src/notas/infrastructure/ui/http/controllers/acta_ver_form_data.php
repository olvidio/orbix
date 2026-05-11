<?php

use src\notas\application\ActaVerFormData;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = ActaVerFormData::execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
