<?php
/**
 * Endpoint backend: datos del formulario de modificacion de una
 * `CartaPresentacion` (valida permisos: solo dl propia o `cr`).
 */

use src\cartaspresentacion\application\CartaPresentacionFormData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$input = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
];
$data = CartaPresentacionFormData::execute($input);
ContestarJson::enviar('', $data);
