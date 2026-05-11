<?php
/**
 * Endpoint backend: datos del formulario modificar/nuevo de
 * `TipoTarifa`.
 */

use src\actividadtarifas\application\TipoTarifaFormData;
use src\shared\web\ContestarJson;

$input = [
    'id_tarifa' => (string)filter_input(INPUT_POST, 'id_tarifa'),
];

$data = TipoTarifaFormData::execute($input);
ContestarJson::enviar('', $data);
