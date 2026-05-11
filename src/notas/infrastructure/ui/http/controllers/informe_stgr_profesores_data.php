<?php

use src\notas\application\InformeStgrProfesores;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];

try {
    $Qlista = (string)filter_input(INPUT_POST, 'lista');
    $lista = !empty($Qlista);

    $informe = new InformeStgrProfesores();
    $data = $informe->calcular($lista);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
