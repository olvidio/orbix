<?php

use src\procesos\application\ProcesosUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosUpdate $useCase */
$useCase = DependencyResolver::get(ProcesosUpdate::class);

ContestarJson::enviar($useCase->execute($_POST));
