<?php

/**
 * Endpoint JSON: datos crudos para la tabla `personas_select`.
 *
 * Ruta: /src/personas/personas_select_data
 *
 * Respuesta: `ContestarJson::enviar($error, $data)` -> `{success, mensaje, data}`.
 *
 * Asocia al caso de uso `PersonasSelectData::build($_POST)` para mantener
 * `frontend/personas/controller/personas_select.php` sin dependencias directas
 * a `src/` (regla de separacion definida en `refactor.md`).
 */

use src\shared\web\ContestarJson;
use src\personas\application\PersonasSelectData;

$result = PersonasSelectData::build($_POST);

if (!empty($result['error'])) {
    ContestarJson::enviar((string)$result['error']);
    return;
}

ContestarJson::enviar('', $result);
