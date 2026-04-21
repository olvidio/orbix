<?php

use src\misas\application\CuadriculaZonaGridData;
use web\ContestarJson;

$in = [
    'id_zona' => (int)filter_input(INPUT_POST, 'id_zona'),
    'tipo_plantilla' => (string)filter_input(INPUT_POST, 'tipo_plantilla'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'orden' => (string)filter_input(INPUT_POST, 'orden'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'fila' => (int)filter_input(INPUT_POST, 'fila'),
    'columna' => (int)filter_input(INPUT_POST, 'columna'),
    'seleccion' => (int)filter_input(INPUT_POST, 'seleccion'),
];

$result = CuadriculaZonaGridData::build($in);

if (!empty($result['error'])) {
    ContestarJson::enviar($result['error'], []);
}

unset($result['error']);
ContestarJson::enviar('', $result);
