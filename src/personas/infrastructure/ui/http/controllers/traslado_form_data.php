<?php

/**
 * Endpoint JSON: datos para el formulario `traslado_form.phtml`.
 *
 * Ruta: /src/personas/traslado_form_data
 *
 * Respuesta: `ContestarJson::enviar($error, $data)`.
 */

use frontend\shared\web\ContestarJson;
use src\personas\application\TrasladoFormData;

$result = TrasladoFormData::build($_POST);

if (!empty($result['error'])) {
    ContestarJson::enviar((string)$result['error']);
    return;
}

ContestarJson::enviar('', $result);
