<?php

/**
 * Endpoint JSON: datos para el formulario `stgr_cambio.phtml`.
 *
 * Ruta: /src/personas/stgr_cambio_data
 *
 * Respuesta: `ContestarJson::enviar($error, $data)`.
 */

use src\shared\web\ContestarJson;
use src\personas\application\StgrCambioData;

$result = StgrCambioData::build($_POST);

if (!empty($result['error'])) {
    ContestarJson::enviar((string)$result['error']);
    return;
}

ContestarJson::enviar('', $result);
