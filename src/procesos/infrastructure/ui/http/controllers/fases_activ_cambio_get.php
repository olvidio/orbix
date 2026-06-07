<?php

use src\procesos\application\FasesActivCambioGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FasesActivCambioGet $useCase */
$useCase = DependencyResolver::get(FasesActivCambioGet::class);

ContestarJson::enviar('', $useCase->execute($_POST));
