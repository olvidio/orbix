<?php
/**
 * Endpoint backend: elimina el sacd ({id_activ, id_cargo}) de una
 * actividad y la asistencia asociada. Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdEliminar;
use web\ContestarJson;

$error_txt = SacdEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
