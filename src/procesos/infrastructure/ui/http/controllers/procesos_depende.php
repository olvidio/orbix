<?php

use src\procesos\application\ProcesosDepende;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosDepende $useCase */
$useCase = DependencyResolver::get(ProcesosDepende::class);

ContestarJson::enviar('', $useCase->execute($_POST));
