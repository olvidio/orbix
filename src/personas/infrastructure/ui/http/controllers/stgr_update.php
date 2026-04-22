<?php

/**
 * Endpoint JSON: actualiza el `nivel_stgr` de una persona.
 *
 * Ruta: /src/personas/stgr_update
 *
 * Respuesta: `ContestarJson::enviar($error, 'ok')` -> `{success, mensaje, data}`
 *
 * Migrado desde `apps/personas/controller/stgr_update.php`
 * (slice 1 de la migracion del modulo `personas`).
 */

use src\personas\application\StgrUpdate;
use web\ContestarJson;

$Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
$Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
$Qnivel_stgr = (string)filter_input(INPUT_POST, 'nivel_stgr');

$error_txt = StgrUpdate::execute($Qid_nom, $Qid_tabla, $Qnivel_stgr);

ContestarJson::enviar($error_txt, 'ok');
