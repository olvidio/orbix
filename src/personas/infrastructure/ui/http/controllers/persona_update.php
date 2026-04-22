<?php

/**
 * Endpoint JSON: guarda los datos de una persona.
 *
 * Ruta: /src/personas/persona_update
 *
 * Respuesta: `ContestarJson::enviar($error, 'ok')` -> `{success, mensaje, data}`
 *
 * Migrado desde la rama "guardar" de `apps/personas/controller/personas_update.php`
 * (slice 2 de la migracion del modulo `personas`).
 */

use src\personas\application\PersonaUpdate;
use web\ContestarJson;

$error_txt = PersonaUpdate::execute($_POST);

ContestarJson::enviar($error_txt, 'ok');
