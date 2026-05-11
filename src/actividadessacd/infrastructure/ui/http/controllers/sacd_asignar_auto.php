<?php
/**
 * Endpoint backend: auto-asignacion masiva del sacd titular del centro
 * encargado a actividades sr/sg sin sacd. Responde JSON
 * `{success, mensaje, data: {asignadas, sin_asignar}}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdAsignarAuto;
use src\shared\web\ContestarJson;

$data = SacdAsignarAuto::execute($_POST);
ContestarJson::enviar('', $data);
