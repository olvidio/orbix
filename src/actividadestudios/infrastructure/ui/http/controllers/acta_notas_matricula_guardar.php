<?php

use src\actividadestudios\application\ActaNotasMatriculaGuardar;
use web\ContestarJson;

/**
 * Guarda el borrador de notas sobre cada matricula (rama `que=1` del legacy
 * `apps/actividadestudios/controller/acta_notas_update.php`).
 */
$error_txt = ActaNotasMatriculaGuardar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
