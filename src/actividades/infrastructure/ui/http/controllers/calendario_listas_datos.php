<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend para `calendario_listas`.
 * Recibe los filtros via POST y delega en CalendarioListasDatos. Devuelve
 * JSON con el HTML listo para inyectar en el DOM.
 */

use src\actividades\application\CalendarioListasDatos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'que' => (string)FilterPostGet::post('que'),
    'ver_ctr' => (string)FilterPostGet::post('ver_ctr'),
    'periodo' => (string)FilterPostGet::post('periodo'),
    'year' => (string)FilterPostGet::post('year'),
    'yeardefault' => (string)FilterPostGet::post('yeardefault'),
    'empiezamin' => (string)FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)FilterPostGet::post('empiezamax'),
    'id_cdc' => (array)FilterPostGet::post('id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

/** @var CalendarioListasDatos $useCase */
$useCase = DependencyResolver::get(CalendarioListasDatos::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
