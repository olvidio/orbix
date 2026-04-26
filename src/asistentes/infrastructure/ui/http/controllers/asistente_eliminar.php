<?php

use src\asistentes\application\AsistenteEliminar;
use frontend\shared\web\ContestarJson;

/**
 * Elimina un `Asistente` y sus matriculas.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = AsistenteEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
