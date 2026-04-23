<?php
/**
 * Endpoint backend: datos del grid comparativo A vs B (plazas
 * concedidas y libres entre dos dl para un tipo de actividad).
 * El HTML lo monta el controller frontend.
 */

use src\actividadplazas\application\PlazasBalanceData;
use web\ContestarJson;

$input = [
    'dl' => (string)filter_input(INPUT_POST, 'dl'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
];

$data = PlazasBalanceData::execute($input);
$error = (string)($data['error'] ?? '');
unset($data['error']);
ContestarJson::enviar($error, $data);
