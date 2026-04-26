<?php
/**
 * Endpoint backend: crea o actualiza una `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaUpdate;
use frontend\shared\web\ContestarJson;

$input = [
    'id_item' => (string)filter_input(INPUT_POST, 'id_item'),
    'id_tarifa' => (int)filter_input(INPUT_POST, 'id_tarifa'),
    'id_tipo_activ' => (int)filter_input(INPUT_POST, 'id_tipo_activ'),
];

$error = RelacionTarifaUpdate::execute($input);
ContestarJson::enviar($error, 'ok');
