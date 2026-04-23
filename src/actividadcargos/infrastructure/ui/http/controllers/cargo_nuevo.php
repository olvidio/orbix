<?php

use src\actividadcargos\application\ActividadCargoNuevo;
use web\ContestarJson;

/**
 * Crea un `ActividadCargo`. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = ActividadCargoNuevo::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
