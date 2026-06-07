<?php

use src\notas\application\ActaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = (DependencyResolver::get(ActaNueva::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
