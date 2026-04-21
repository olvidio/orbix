<?php

use src\misas\application\ZonaSacdDatosPut;
use web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd');

$result = ZonaSacdDatosPut::execute($Qid_zona, $Qid_sacd, [
    'dw1' => (string)filter_input(INPUT_POST, 'dw1'),
    'dw2' => (string)filter_input(INPUT_POST, 'dw2'),
    'dw3' => (string)filter_input(INPUT_POST, 'dw3'),
    'dw4' => (string)filter_input(INPUT_POST, 'dw4'),
    'dw5' => (string)filter_input(INPUT_POST, 'dw5'),
    'dw6' => (string)filter_input(INPUT_POST, 'dw6'),
    'dw7' => (string)filter_input(INPUT_POST, 'dw7'),
]);

ContestarJson::enviar((string)($result['error'] ?? ''));
