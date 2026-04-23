<?php

use src\actividadcargos\application\ActividadCargoEliminar;
use web\ContestarJson;

/**
 * Elimina un `ActividadCargo` y, si procede, su `Asistente`.
 * Responde JSON `{success, mensaje, data}`.
 */
$error_txt = ActividadCargoEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
