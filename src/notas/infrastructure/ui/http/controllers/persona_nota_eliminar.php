<?php

use src\notas\application\PersonaNotaEliminar;
use src\shared\web\ContestarJson;

/**
 * Elimina una `PersonaNota`. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = PersonaNotaEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
