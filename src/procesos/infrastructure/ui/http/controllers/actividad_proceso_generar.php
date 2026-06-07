<?php

use src\procesos\application\ActividadProcesoGenerar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadProcesoGenerar $useCase */
$useCase = DependencyResolver::get(ActividadProcesoGenerar::class);

ContestarJson::enviar($useCase->execute($_POST));
