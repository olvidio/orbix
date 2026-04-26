<?php

/**
 * Endpoint JSON: aplica un traslado de centro y/o delegacion.
 *
 * Ruta: /src/personas/traslado_update
 *
 * Respuesta: `ContestarJson::enviar($error, 'ok')` -> `{success, mensaje, data}`
 *
 * Migrado desde `apps/personas/controller/traslado_update.php` (slice 5 de
 * la migracion del modulo `personas`).
 */

use src\personas\application\TrasladoUpdate;
use frontend\shared\web\ContestarJson;

$error_txt = TrasladoUpdate::execute($_POST);

ContestarJson::enviar($error_txt, 'ok');
