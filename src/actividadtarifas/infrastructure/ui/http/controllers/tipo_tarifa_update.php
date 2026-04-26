<?php
/**
 * Endpoint backend: crea o actualiza un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaUpdate;
use frontend\shared\web\ContestarJson;

$input = [
    'id_tarifa' => (string)filter_input(INPUT_POST, 'id_tarifa'),
    'letra' => (string)filter_input(INPUT_POST, 'letra'),
    'modo' => (string)filter_input(INPUT_POST, 'modo'),
    'observ' => (string)filter_input(INPUT_POST, 'observ'),
];

$error = TipoTarifaUpdate::execute($input);
ContestarJson::enviar($error, 'ok');
