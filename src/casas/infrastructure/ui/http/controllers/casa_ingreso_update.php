<?php
/**
 * Endpoint backend: crear/actualizar el Ingreso de una actividad.
 */

use src\casas\application\CasaIngresoUpdate;
use frontend\shared\web\ContestarJson;

$input = [
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
    'id_tarifa' => filter_input(INPUT_POST, 'id_tarifa'),
    'precio' => filter_input(INPUT_POST, 'precio'),
    'ingresos' => filter_input(INPUT_POST, 'ingresos'),
    'num_asistentes' => filter_input(INPUT_POST, 'num_asistentes'),
    'observ' => (string)filter_input(INPUT_POST, 'observ'),
];
$result = CasaIngresoUpdate::execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', $result['data'] ?? '');
} else {
    ContestarJson::enviar($result['mensaje'] ?? 'error', '');
}
