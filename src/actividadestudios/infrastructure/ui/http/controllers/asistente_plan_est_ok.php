<?php

use src\actividadestudios\application\AsistentePlanEstOk;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var AsistentePlanEstOk $useCase */
$useCase = DependencyResolver::get(AsistentePlanEstOk::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
