<?php

use src\notas\application\ActaModificar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = (DependencyResolver::get(ActaModificar::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
