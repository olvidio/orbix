<?php
/**
 * Endpoint backend para `calendario_listas`.
 * Recibe los filtros via POST y delega en CalendarioListasDatos. Devuelve
 * JSON con el HTML listo para inyectar en el DOM.
 */

use src\actividades\application\CalendarioListasDatos;
use src\shared\web\ContestarJson;

$input = [
    'que' => (string)filter_input(INPUT_POST, 'que'),
    'ver_ctr' => (string)filter_input(INPUT_POST, 'ver_ctr'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'yeardefault' => (string)filter_input(INPUT_POST, 'yeardefault'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

$useCase = new CalendarioListasDatos();
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
