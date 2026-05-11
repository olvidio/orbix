<?php
/**
 * Endpoint backend: actualiza el array `cedidas` de
 * `ActividadPlazasDl` para ceder (o quitar) plazas de `mi_dele`
 * a otra dl en una actividad.
 */

use src\actividadplazas\application\PlazasCeder;
use src\shared\web\ContestarJson;

$input = [
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
    'num_plazas' => (int)filter_input(INPUT_POST, 'num_plazas'),
    'region_dl' => (string)filter_input(INPUT_POST, 'region_dl'),
];

$error = PlazasCeder::execute($input);
ContestarJson::enviar($error, 'ok');
