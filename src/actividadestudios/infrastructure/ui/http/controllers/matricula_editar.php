<?php

use src\actividadestudios\application\MatriculaEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var MatriculaEditar $useCase */
$useCase = DependencyResolver::get(MatriculaEditar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
