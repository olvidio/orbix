<?php

use src\actividadestudios\application\MatriculaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var MatriculaEliminar $useCase */
$useCase = DependencyResolver::get(MatriculaEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
