<?php
/**
 * Endpoint backend: crea o actualiza una `TarifaUbi`.
 */

use src\actividadtarifas\application\TarifaUbiUpdate;
use web\ContestarJson;

$input = [
    'id_item' => (int)filter_input(INPUT_POST, 'id_item'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (int)filter_input(INPUT_POST, 'year'),
    'id_tarifa' => (int)filter_input(INPUT_POST, 'id_tarifa'),
    'id_serie' => (int)filter_input(INPUT_POST, 'id_serie'),
    'cantidad' => (string)filter_input(INPUT_POST, 'cantidad'),
    'observ' => (string)filter_input(INPUT_POST, 'observ'),
];

$error = TarifaUbiUpdate::execute($input);
ContestarJson::enviar($error, 'ok');
