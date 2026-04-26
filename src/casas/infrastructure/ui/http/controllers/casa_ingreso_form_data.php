<?php
/**
 * Endpoint backend: datos para el formulario de ingreso de una
 * actividad (`casa_ingreso_form`).
 */

use src\casas\application\CasaIngresoFormData;
use frontend\shared\web\ContestarJson;

$input = [
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
];
$data = CasaIngresoFormData::execute($input);
ContestarJson::enviar('', $data);
