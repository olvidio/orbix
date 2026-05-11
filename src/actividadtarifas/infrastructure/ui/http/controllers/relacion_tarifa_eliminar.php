<?php
/**
 * Endpoint backend: elimina una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaEliminar;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => (int)filter_input(INPUT_POST, 'id_item'),
];

$error = RelacionTarifaEliminar::execute($input);
ContestarJson::enviar($error, 'ok');
