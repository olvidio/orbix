<?php

use src\procesos\application\UsuarioPermActivData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var UsuarioPermActivData $useCase */
$useCase = DependencyResolver::get(UsuarioPermActivData::class);

ContestarJson::enviar('', $useCase->execute($_POST));
