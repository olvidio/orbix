<?php

use src\procesos\application\ProcesosGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosGet $useCase */
$useCase = DependencyResolver::get(ProcesosGet::class);

ContestarJson::enviar('', $useCase->execute($_POST));
