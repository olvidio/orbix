<?php

use src\procesos\application\FasesActivCambioUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FasesActivCambioUpdate $useCase */
$useCase = DependencyResolver::get(FasesActivCambioUpdate::class);

ContestarJson::enviar($useCase->execute($_POST));
