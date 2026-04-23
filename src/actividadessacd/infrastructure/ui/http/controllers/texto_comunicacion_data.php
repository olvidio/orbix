<?php
/**
 * Endpoint backend: devuelve el texto de comunicacion (`clave`, `idioma`).
 * Responde JSON `{success, mensaje, data: {texto}}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\TextoComunicacionData;
use web\ContestarJson;

$data = TextoComunicacionData::execute($_POST);
ContestarJson::enviar('', $data);
