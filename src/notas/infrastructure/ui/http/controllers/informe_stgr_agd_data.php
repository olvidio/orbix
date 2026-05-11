<?php

use src\notas\application\InformeStgrAgregados;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    $Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qlista = (string)filter_input(INPUT_POST, 'lista');
    $lista = !empty($Qlista);

    $informe = new InformeStgrAgregados();
    $data = $informe->calcular($Qdl, $lista);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
