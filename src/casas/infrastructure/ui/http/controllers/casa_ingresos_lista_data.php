<?php
/**
 * Endpoint backend: listado económico de actividades por casa
 * (`casa_ingresos_lista`).
 */

use src\casas\application\CasaIngresosListaData;
use web\ContestarJson;

$input = [
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];
$data = CasaIngresosListaData::execute($input);
ContestarJson::enviar('', $data);
