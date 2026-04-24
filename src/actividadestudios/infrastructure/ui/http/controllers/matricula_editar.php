<?php

use src\actividadestudios\application\MatriculaEditar;
use web\ContestarJson;

/**
 * Edita una matricula. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = MatriculaEditar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
