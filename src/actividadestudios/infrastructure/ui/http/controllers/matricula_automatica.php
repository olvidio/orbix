<?php

use src\actividadestudios\application\MatriculaAutomatica;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var MatriculaAutomatica $useCase */
$useCase = DependencyResolver::get(MatriculaAutomatica::class);
$msg = $useCase->execute($_POST);
ContestarJson::enviar($msg, 'ok');
