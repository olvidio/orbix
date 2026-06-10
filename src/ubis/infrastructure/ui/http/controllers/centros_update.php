<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\application\CentrosUpdate;

/** @var CentrosUpdate $useCase */
$useCase = DependencyResolver::get(CentrosUpdate::class);

$error = $useCase->execute($_POST);
ContestarJson::enviar($error, 'ok');
