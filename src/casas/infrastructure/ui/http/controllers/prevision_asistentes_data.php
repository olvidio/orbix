<?php
/**
 * Endpoint backend: datos de la pantalla `prevision_asistentes`.
 */

use src\casas\application\PrevisionAsistentesData;
use web\ContestarJson;

$input = [
    'mi_of' => (string)filter_input(INPUT_POST, 'mi_of'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'inicio_iso' => (string)filter_input(INPUT_POST, 'inicio_iso'),
    'fin_iso' => (string)filter_input(INPUT_POST, 'fin_iso'),
];

$data = PrevisionAsistentesData::execute($input);
ContestarJson::enviar('', $data);
