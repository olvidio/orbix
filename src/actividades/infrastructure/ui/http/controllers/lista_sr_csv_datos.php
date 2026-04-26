<?php
/**
 * Endpoint backend para `lista_sr_csv` (listado SR + exportacion).
 * Recibe los filtros via POST y delega en ListaSrCsvListado. Devuelve JSON
 * con html_tabla y los datos crudos para poder exportar a CSV desde frontend.
 */

use src\actividades\application\ListaSrCsvListado;
use frontend\shared\web\ContestarJson;

$input = [
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'c_activ' => (array)filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'status' => (array)filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

$useCase = new ListaSrCsvListado();
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
