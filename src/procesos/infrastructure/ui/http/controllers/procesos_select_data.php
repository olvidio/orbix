<?php

use src\procesos\application\ProcesosSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProcesosSelectData $useCase */
$useCase = DependencyResolver::get(ProcesosSelectData::class);

ContestarJson::enviar('', $useCase->execute());
