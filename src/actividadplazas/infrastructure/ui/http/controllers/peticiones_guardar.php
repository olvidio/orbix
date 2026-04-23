<?php
/**
 * Endpoint backend: guarda las peticiones de una persona+tipo
 * (borra las anteriores y crea las nuevas en orden).
 */

use src\actividadplazas\application\PeticionesGuardar;
use web\ContestarJson;

$a_actividades = (array)filter_input(INPUT_POST, 'actividades', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$input = [
    'id_nom' => (int)filter_input(INPUT_POST, 'id_nom'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'actividades' => $a_actividades,
];

$error = PeticionesGuardar::execute($input);
ContestarJson::enviar($error, 'ok');
