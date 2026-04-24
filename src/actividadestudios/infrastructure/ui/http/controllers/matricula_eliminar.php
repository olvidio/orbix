<?php

use src\actividadestudios\application\MatriculaEliminar;
use web\ContestarJson;

/**
 * Elimina una o varias matriculas. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = MatriculaEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
