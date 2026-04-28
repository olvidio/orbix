<?php

use frontend\shared\web\ContestarJson;
use src\actividadessacd\application\ComSacdActivPeriodoPageData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];
try {
    $data = ComSacdActivPeriodoPageData::execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
