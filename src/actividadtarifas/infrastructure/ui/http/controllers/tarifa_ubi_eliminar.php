<?php
/**
 * Endpoint backend: elimina una `TarifaUbi`.
 */

use src\actividadtarifas\application\TarifaUbiEliminar;
use web\ContestarJson;

$input = [
    'id_item' => (int)filter_input(INPUT_POST, 'id_item'),
];

$error = TarifaUbiEliminar::execute($input);
ContestarJson::enviar($error, 'ok');
