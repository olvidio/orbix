<?php
/**
 * Endpoint backend: datos del formulario modificar/nuevo de
 * `RelacionTarifaTipoActividad`.
 */

use src\actividadtarifas\application\RelacionTarifaFormData;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => (string)filter_input(INPUT_POST, 'id_item'),
];

$data = RelacionTarifaFormData::execute($input);
ContestarJson::enviar('', $data);
