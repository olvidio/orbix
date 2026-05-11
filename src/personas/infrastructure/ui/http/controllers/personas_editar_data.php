<?php

/**
 * Endpoint JSON: datos para la ficha `personas_editar.phtml`.
 *
 * Ruta: /src/personas/personas_editar_data
 *
 * Respuesta: `ContestarJson::enviar($error, $data)`.
 */

use src\shared\web\ContestarJson;
use src\personas\application\PersonasEditarData;

$result = PersonasEditarData::build($_POST);

if (!empty($result['error'])) {
    ContestarJson::enviar((string)$result['error']);
    return;
}

ContestarJson::enviar('', $result);
