<?php
/**
 * Endpoint backend: elimina un `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaEliminar;
use frontend\shared\web\ContestarJson;

$input = [
    'id_tarifa' => (int)filter_input(INPUT_POST, 'id_tarifa'),
];

$error = TipoTarifaEliminar::execute($input);
ContestarJson::enviar($error, 'ok');
