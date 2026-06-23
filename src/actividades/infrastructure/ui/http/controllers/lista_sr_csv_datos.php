<?php
/**
 * Endpoint backend para `lista_sr_csv` (listado SR + exportacion).
 * Recibe los filtros via POST y delega en ListaSrCsvListado. Devuelve JSON
 * con html_tabla y los datos crudos para poder exportar a CSV desde frontend.
 */

use src\actividades\application\ListaSrCsvListado;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'periodo' => (string)filter_post('periodo'),
    'year' => (string)filter_post('year'),
    'dl_org' => (string)filter_post('dl_org'),
    'empiezamin' => (string)filter_post('empiezamin'),
    'empiezamax' => (string)filter_post('empiezamax'),
    'c_activ' => (array)filter_post('c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'status' => (array)filter_post('status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'id_cdc' => (array)filter_post('id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

/** @var ListaSrCsvListado $useCase */
$useCase = DependencyResolver::get(ListaSrCsvListado::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
