<?php

use src\procesos\application\ProcesosGetListado;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosGetListado $useCase */
$useCase = DependencyResolver::get(ProcesosGetListado::class);

ContestarJson::enviar('', $useCase->execute($_POST));
