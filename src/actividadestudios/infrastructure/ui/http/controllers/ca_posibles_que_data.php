<?php

use src\actividadestudios\application\CaPosiblesQueData;
use frontend\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $data = CaPosiblesQueData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
