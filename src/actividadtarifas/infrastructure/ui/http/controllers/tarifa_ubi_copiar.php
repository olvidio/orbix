<?php
/**
 * Endpoint backend: copiar tarifas del año anterior.
 * Vease `TarifaUbiCopiar` — accion heredada rota, pendiente de
 * reimplementar.
 */

use src\actividadtarifas\application\TarifaUbiCopiar;
use web\ContestarJson;

$input = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (int)filter_input(INPUT_POST, 'year'),
];

$error = TarifaUbiCopiar::execute($input);
ContestarJson::enviar($error, 'ok');
