<?php

use src\procesos\application\UsuarioPermActivFases;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var UsuarioPermActivFases $useCase */
$useCase = DependencyResolver::get(UsuarioPermActivFases::class);

ContestarJson::enviar('', $useCase->execute($_POST));
