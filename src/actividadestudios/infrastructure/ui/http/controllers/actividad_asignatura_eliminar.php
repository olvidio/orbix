<?php

use src\actividadestudios\application\ActividadAsignaturaEliminar;
use src\shared\web\ContestarJson;

$error_txt = ActividadAsignaturaEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
