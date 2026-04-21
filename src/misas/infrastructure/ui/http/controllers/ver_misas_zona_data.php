<?php

use src\misas\application\VerMisasZonaData;
use web\ContestarJson;

$in = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
];

$result = VerMisasZonaData::build($in);

$error = (string)($result['error'] ?? '');
unset($result['error']);

ContestarJson::enviar($error, $result);
