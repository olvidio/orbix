<?php
/**
 * Endpoint backend: datos del resumen de plazas por actividad
 * (calendario/cedidas/conseguidas/disponibles/ocupadas por dl) +
 * opciones del desplegable para "ceder" y flags publicado/otra_dl.
 */

use src\actividadplazas\application\ResumenPlazasData;
use web\ContestarJson;

$input = [
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
    'nom_activ' => (string)filter_input(INPUT_POST, 'nom_activ'),
];

$data = ResumenPlazasData::execute($input);
$error = (string)($data['error'] ?? '');
unset($data['error']);
ContestarJson::enviar($error, $data);
