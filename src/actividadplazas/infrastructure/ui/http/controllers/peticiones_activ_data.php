<?php
/**
 * Endpoint backend: lista de actividades candidatas + peticiones
 * actuales para una persona+tipo. Limpia del repo las peticiones
 * que ya no esten en la lista (mismo comportamiento que legacy).
 */

use src\actividadplazas\application\PeticionesActivData;
use frontend\shared\web\ContestarJson;

$input = [
    'id_nom' => (int)filter_input(INPUT_POST, 'id_nom'),
    'na' => (string)filter_input(INPUT_POST, 'na'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'todos' => (int)filter_input(INPUT_POST, 'todos'),
    'id_ctr_agd' => (int)filter_input(INPUT_POST, 'id_ctr_agd'),
    'id_ctr_n' => (int)filter_input(INPUT_POST, 'id_ctr_n'),
];

$data = PeticionesActivData::execute($input);
ContestarJson::enviar('', $data);
