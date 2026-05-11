<?php
/**
 * Endpoint backend: encola los mails de comunicacion de actividades a
 * los sacd y al ctr del sacd, con copia al jefe de calendario. Responde
 * JSON `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\ComunicacionActividadesSacdEnviar;
use src\shared\web\ContestarJson;

$error_txt = ComunicacionActividadesSacdEnviar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
