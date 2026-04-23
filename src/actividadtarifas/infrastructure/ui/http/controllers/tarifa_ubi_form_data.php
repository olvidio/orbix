<?php
/**
 * Endpoint backend: datos del formulario modificar/nuevo de
 * `TarifaUbi`.
 */

use src\actividadtarifas\application\TarifaUbiFormData;
use web\ContestarJson;

$input = [
    'id_item' => (string)filter_input(INPUT_POST, 'id_item'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (int)filter_input(INPUT_POST, 'year'),
    'letra' => (string)filter_input(INPUT_POST, 'letra'),
];

$data = TarifaUbiFormData::execute($input);
ContestarJson::enviar('', $data);
