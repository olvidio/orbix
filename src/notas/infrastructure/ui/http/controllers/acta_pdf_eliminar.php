<?php

use src\notas\application\ActaPdfEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = (DependencyResolver::get(ActaPdfEliminar::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
