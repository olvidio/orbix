<?php

use src\procesos\application\ActividadProcesoUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadProcesoUpdate $useCase */
$useCase = DependencyResolver::get(ActividadProcesoUpdate::class);

ContestarJson::enviar($useCase->execute($_POST));
