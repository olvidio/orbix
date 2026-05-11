<?php

use src\asistentes\application\AsistentePlazaAsignar;
use src\shared\web\ContestarJson;

/**
 * Cambia la plaza de un lote de asistentes.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = AsistentePlazaAsignar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
