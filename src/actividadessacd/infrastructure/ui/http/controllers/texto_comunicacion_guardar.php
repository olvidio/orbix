<?php
/**
 * Endpoint backend: upsert/delete del texto de comunicacion
 * (`clave`, `idioma`, `texto`). Si `texto === ''` elimina la fila.
 * Responde JSON `{success, mensaje, data:'ok'}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\TextoComunicacionGuardar;
use web\ContestarJson;

$error_txt = TextoComunicacionGuardar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
