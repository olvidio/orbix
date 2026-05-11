<?php

use src\actividadestudios\application\ActividadAsignaturaEditar;
use src\shared\web\ContestarJson;

$error_txt = ActividadAsignaturaEditar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
