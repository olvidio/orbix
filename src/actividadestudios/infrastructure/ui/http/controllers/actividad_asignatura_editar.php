<?php

use src\actividadestudios\application\ActividadAsignaturaEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadAsignaturaEditar $useCase */
$useCase = DependencyResolver::get(ActividadAsignaturaEditar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
