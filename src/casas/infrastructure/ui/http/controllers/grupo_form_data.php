<?php
/**
 * Endpoint backend: datos del formulario `GrupoCasa` (nuevo/editar).
 */

use src\casas\application\GrupoCasaFormData;
use web\ContestarJson;

$input = [
    'id_item' => (string)filter_input(INPUT_POST, 'id_item'),
];

$data = GrupoCasaFormData::execute($input);
ContestarJson::enviar('', $data);
