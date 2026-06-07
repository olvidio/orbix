<?php

use src\procesos\application\TipoActivProcesoAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivProcesoAsignar $useCase */
$useCase = DependencyResolver::get(TipoActivProcesoAsignar::class);

ContestarJson::enviar($useCase->execute($_POST));
