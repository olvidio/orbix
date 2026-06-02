<?php

use src\asistentes\application\AsistenteEliminar;
use src\shared\web\ContestarJson;

/**
 * Elimina un `Asistente` y sus matriculas.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = $GLOBALS['container']->get(AsistenteEliminar::class)->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
