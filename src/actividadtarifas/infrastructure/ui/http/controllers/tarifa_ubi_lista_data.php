<?php
/**
 * Endpoint backend: listado de `TarifaUbi` por `id_ubi` + `year`.
 */

use src\actividadtarifas\application\TarifaUbiListaData;
use web\ContestarJson;

$input = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'year' => (int)filter_input(INPUT_POST, 'year'),
];

$data = TarifaUbiListaData::execute($input);
ContestarJson::enviar('', $data);
