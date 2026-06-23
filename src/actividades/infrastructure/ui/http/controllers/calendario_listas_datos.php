<?php
/**
 * Endpoint backend para `calendario_listas`.
 * Recibe los filtros via POST y delega en CalendarioListasDatos. Devuelve
 * JSON con el HTML listo para inyectar en el DOM.
 */

use src\actividades\application\CalendarioListasDatos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'que' => (string)filter_post('que'),
    'ver_ctr' => (string)filter_post('ver_ctr'),
    'periodo' => (string)filter_post('periodo'),
    'year' => (string)filter_post('year'),
    'yeardefault' => (string)filter_post('yeardefault'),
    'empiezamin' => (string)filter_post('empiezamin'),
    'empiezamax' => (string)filter_post('empiezamax'),
    'id_cdc' => (array)filter_post('id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

/** @var CalendarioListasDatos $useCase */
$useCase = DependencyResolver::get(CalendarioListasDatos::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
