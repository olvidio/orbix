<?php

use src\asistentes\application\AsistenteGuardar;
use src\shared\web\ContestarJson;

/**
 * Crea, edita o mueve un `Asistente`.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = $GLOBALS['container']->get(AsistenteGuardar::class)->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
