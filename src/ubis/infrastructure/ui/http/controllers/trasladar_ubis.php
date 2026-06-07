<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TrasladarUbis;
use src\shared\web\ContestarJson;

$errorTxt = DependencyResolver::get(TrasladarUbis::class)->execute($_POST);
ContestarJson::enviar($errorTxt, ['ok' => true]);
