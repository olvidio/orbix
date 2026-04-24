<?php

use src\actividadestudios\application\MatriculaNueva;
use web\ContestarJson;

/**
 * Crea una matricula. Responde JSON `{success, mensaje, data}`.
 */
$error_txt = MatriculaNueva::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
