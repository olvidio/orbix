<?php
/**
 * Endpoint backend: actualiza `num_asistentes_previstos` de un
 * `Ingreso` desde la `TablaEditable` de `prevision_asistentes`.
 */

use src\casas\application\IngresoPlazasPrevistasUpdate;
use web\ContestarJson;

$input = [
    'data' => (string)filter_input(INPUT_POST, 'data'),
    'colName' => (string)filter_input(INPUT_POST, 'colName'),
];

$error = IngresoPlazasPrevistasUpdate::execute($input);
ContestarJson::enviar($error, 'ok');
