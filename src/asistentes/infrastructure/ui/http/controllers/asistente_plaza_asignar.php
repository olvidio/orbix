<?php

use src\asistentes\application\AsistentePlazaAsignar;
use src\shared\web\ContestarJson;

/**
 * Cambia la plaza de un lote de asistentes.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = $GLOBALS['container']->get(AsistentePlazaAsignar::class)->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
