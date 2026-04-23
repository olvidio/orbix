<?php
/**
 * Endpoint backend: incorpora las primeras peticiones de plaza de
 * cada persona como asistencia con plaza asignada/pedida (segun si
 * la actividad es de midele o de otra dl).
 */

use src\actividadplazas\application\PeticionesIncorporar;
use web\ContestarJson;

$input = [
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
];

$result = PeticionesIncorporar::execute($input);
$error = (string)($result['error'] ?? '');
unset($result['error']);
ContestarJson::enviar($error, $result);
