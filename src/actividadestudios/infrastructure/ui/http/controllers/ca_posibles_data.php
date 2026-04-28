<?php

use src\actividadestudios\application\CaPosiblesData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = CaPosiblesData::execute($_POST);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
