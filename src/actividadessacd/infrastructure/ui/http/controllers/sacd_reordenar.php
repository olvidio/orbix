<?php
/**
 * Endpoint backend: reordena un sacd dentro de una actividad (mas / menos
 * prioridad). Responde JSON `{success, mensaje, data}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdReordenar;
use web\ContestarJson;

$error_txt = SacdReordenar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
