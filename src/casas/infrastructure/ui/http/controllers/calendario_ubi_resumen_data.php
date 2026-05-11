<?php
/**
 * Endpoint backend: datos del estudio económico de una casa
 * (`calendario_ubi_resumen`).
 */

use src\casas\application\CalendarioUbiResumenData;
use src\shared\web\ContestarJson;

$input = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'seccion' => (string)filter_input(INPUT_POST, 'seccion'),
    'G' => (int)filter_input(INPUT_POST, 'G'),
    'inc_t' => (int)filter_input(INPUT_POST, 'inc_t'),
];

$data = CalendarioUbiResumenData::execute($input);
ContestarJson::enviar('', $data);
