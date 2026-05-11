<?php
/**
 * Endpoint backend: elimina todas las peticiones de una
 * persona+tipo.
 */

use src\actividadplazas\application\PeticionesEliminar;
use src\shared\web\ContestarJson;

$input = [
    'id_nom' => (int)filter_input(INPUT_POST, 'id_nom'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
];

$error = PeticionesEliminar::execute($input);
ContestarJson::enviar($error, 'ok');
