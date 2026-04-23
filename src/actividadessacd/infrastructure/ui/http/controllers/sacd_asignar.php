<?php
/**
 * Endpoint backend: asigna un sacd a una actividad (y, si es sv, tambien
 * crea la asistencia). Responde JSON `{success, mensaje, data}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdAsignar;
use web\ContestarJson;

$error_txt = SacdAsignar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
