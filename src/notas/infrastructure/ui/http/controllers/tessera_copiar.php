<?php

use src\notas\application\TesseraCopiar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = (DependencyResolver::get(TesseraCopiar::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
