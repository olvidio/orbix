<?php

use src\actividadestudios\application\ActividadAsignaturaNueva;
use src\shared\web\ContestarJson;

$error_txt = ActividadAsignaturaNueva::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
