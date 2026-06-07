<?php

use src\procesos\application\ProcesosClonar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosClonar $useCase */
$useCase = DependencyResolver::get(ProcesosClonar::class);

ContestarJson::enviar($useCase->execute($_POST));
