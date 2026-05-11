<?php

use src\shared\web\ContestarJson;
use src\notas\application\TesseraVerData;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $data = TesseraVerData::execute($id_nom);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
