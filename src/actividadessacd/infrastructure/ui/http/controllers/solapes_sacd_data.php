<?php
/**
 * Endpoint backend: devuelve el listado de sacd con actividades
 * incompatibles (solapes) en el periodo.
 */

use src\actividadessacd\application\SolapesSacdData;
use web\ContestarJson;

$input = [
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

$data = SolapesSacdData::execute($input);
ContestarJson::enviar('', $data);
