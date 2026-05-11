<?php

use src\shared\web\ContestarJson;
use src\notas\application\TesseraImprimirData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $data = TesseraImprimirData::execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
