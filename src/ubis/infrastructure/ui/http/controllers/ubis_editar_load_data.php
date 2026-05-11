<?php

use src\shared\web\ContestarJson;
use src\ubis\application\UbisEditarLoadData;

$error = '';
$data = [];
try {
    $data = UbisEditarLoadData::execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
