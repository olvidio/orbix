<?php

use src\actividadestudios\application\MatriculaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var MatriculaNueva $useCase */
$useCase = DependencyResolver::get(MatriculaNueva::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
