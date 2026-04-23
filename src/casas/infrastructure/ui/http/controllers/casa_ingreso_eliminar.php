<?php
/**
 * Endpoint backend: eliminar el Ingreso de una actividad.
 */

use src\casas\application\CasaIngresoEliminar;
use web\ContestarJson;

$input = [
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
];
$result = CasaIngresoEliminar::execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', $result['data'] ?? '');
} else {
    ContestarJson::enviar($result['mensaje'] ?? 'error', '');
}
