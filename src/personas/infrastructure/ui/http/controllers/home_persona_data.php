<?php

/**
 * Endpoint JSON: datos para la pantalla `home_persona.phtml`.
 *
 * Ruta: /src/personas/home_persona_data
 *
 * Respuesta: `ContestarJson::enviar($error, $data)`.
 */

use src\shared\web\ContestarJson;
use src\personas\application\HomePersonaData;

$result = HomePersonaData::build($_POST);

if (!empty($result['error'])) {
    ContestarJson::enviar((string)$result['error']);
    return;
}

ContestarJson::enviar('', $result);
