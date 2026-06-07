<?php

use src\procesos\application\ProcesosRegenerar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosRegenerar $useCase */
$useCase = DependencyResolver::get(ProcesosRegenerar::class);

ContestarJson::enviar($useCase->execute($_POST));
