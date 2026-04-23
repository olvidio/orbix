<?php

use src\casas\application\CasaEcGastosGuardar;
use web\ContestarJson;

$input = array_merge($_POST, [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (int)filter_input(INPUT_POST, 'year'),
]);
$result = CasaEcGastosGuardar::execute($input);
ContestarJson::enviar(
    $result['ok'] ? '' : ($result['mensaje'] ?? 'error'),
    $result['data'] ?? ''
);
