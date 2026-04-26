<?php
/**
 * Endpoint backend: listado de actividades por casa y periodo
 * (`casa_actividades_lista`).
 */

use src\casas\application\CasaActividadesListaData;
use frontend\shared\web\ContestarJson;

$input = [
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];
$data = CasaActividadesListaData::execute($input);
ContestarJson::enviar('', $data);
