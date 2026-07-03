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
    'que' => (string)\src\shared\domain\helpers\FilterPostGet::post('que'),
    'ver_ctr' => (string)\src\shared\domain\helpers\FilterPostGet::post('ver_ctr'),
    'periodo' => (string)\src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'year' => (string)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'yeardefault' => (string)\src\shared\domain\helpers\FilterPostGet::post('yeardefault'),
    'empiezamin' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'id_cdc' => (array)\src\shared\domain\helpers\FilterPostGet::post('id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

/** @var CalendarioListasDatos $useCase */
$useCase = DependencyResolver::get(CalendarioListasDatos::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
