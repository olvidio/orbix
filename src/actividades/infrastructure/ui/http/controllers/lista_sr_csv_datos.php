<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend para `lista_sr_csv` (listado SR + exportacion).
 * Recibe los filtros via POST y delega en ListaSrCsvListado. Devuelve JSON
 * con html_tabla y los datos crudos para poder exportar a CSV desde frontend.
 */

use src\actividades\application\ListaSrCsvListado;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'periodo' => (string)\src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'year' => (string)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'dl_org' => (string)\src\shared\domain\helpers\FilterPostGet::post('dl_org'),
    'empiezamin' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'c_activ' => (array)\src\shared\domain\helpers\FilterPostGet::post('c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'status' => (array)\src\shared\domain\helpers\FilterPostGet::post('status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'id_cdc' => (array)\src\shared\domain\helpers\FilterPostGet::post('id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

/** @var ListaSrCsvListado $useCase */
$useCase = DependencyResolver::get(ListaSrCsvListado::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
