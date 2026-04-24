<?php

use src\actividadestudios\application\ActividadAsignaturaEliminar;
use web\ContestarJson;

$error_txt = ActividadAsignaturaEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
