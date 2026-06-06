<?php

use src\actividadestudios\application\ActividadAsignaturaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadAsignaturaNueva $useCase */
$useCase = DependencyResolver::get(ActividadAsignaturaNueva::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
