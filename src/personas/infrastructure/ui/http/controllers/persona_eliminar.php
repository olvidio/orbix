<?php

/**
 * Endpoint JSON: elimina una persona.
 *
 * Ruta: /src/personas/persona_eliminar
 *
 * Respuesta: `ContestarJson::enviar($error, 'ok')` -> `{success, mensaje, data}`
 *
 * Migrado desde la rama "eliminar" de `apps/personas/controller/personas_update.php`
 * (slice 2 de la migracion del modulo `personas`).
 */

use src\personas\application\PersonaEliminar;
use web\ContestarJson;

$Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$error_txt = PersonaEliminar::execute($Qid_nom, $Qobj_pau);

ContestarJson::enviar($error_txt, 'ok');
