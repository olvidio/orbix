<?php

use src\procesos\application\ProcesosEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosEliminar $useCase */
$useCase = DependencyResolver::get(ProcesosEliminar::class);

ContestarJson::enviar($useCase->execute($_POST));
