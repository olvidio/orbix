<?php

use src\misas\application\CrearNuevoPeriodoData;
use frontend\shared\web\ContestarJson;

$in = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla' => (string)filter_input(INPUT_POST, 'tipo_plantilla'),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'orden' => (string)filter_input(INPUT_POST, 'orden'),
];

$result = CrearNuevoPeriodoData::build($in);

$error = (string)($result['error'] ?? '');
unset($result['error']);

ContestarJson::enviar($error, $result);
