<?php

use src\actividadestudios\application\ActaNotasMatriculaGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActaNotasMatriculaGuardar $useCase */
$useCase = DependencyResolver::get(ActaNotasMatriculaGuardar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
