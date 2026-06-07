<?php

use src\notas\application\ActaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = (DependencyResolver::get(ActaEliminar::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
