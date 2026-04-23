<?php

use src\casas\application\CasaEcGastosFormData;
use web\ContestarJson;

$input = [
    'year' => (int)filter_input(INPUT_POST, 'year'),
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];
$data = CasaEcGastosFormData::execute($input);
ContestarJson::enviar('', $data);
