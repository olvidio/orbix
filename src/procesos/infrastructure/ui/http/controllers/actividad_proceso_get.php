<?php

use src\procesos\application\ActividadProcesoGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadProcesoGet $useCase */
$useCase = DependencyResolver::get(ActividadProcesoGet::class);

ContestarJson::enviar('', $useCase->execute($_POST));
