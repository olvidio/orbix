<?php

use src\shared\web\ContestarJson;
use src\actividadessacd\application\LocalesDesplegableData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];
try {
    $data = LocalesDesplegableData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
