<?php

use src\casas\application\CasasResumenData;
use src\shared\web\ContestarJson;

$input = [
    'que' => (string)filter_input(INPUT_POST, 'que'),
    'cdc_sel' => (int)filter_input(INPUT_POST, 'cdc_sel'),
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];
$data = CasasResumenData::execute($input);
ContestarJson::enviar('', $data);
