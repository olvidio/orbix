<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionUpdate;
use src\shared\web\ContestarJson;

$errorTxt = DependencyResolver::get(DireccionUpdate::class)->execute($_POST);
ContestarJson::enviar($errorTxt, ['ok' => true]);
