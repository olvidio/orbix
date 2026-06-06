<?php

use src\actividadestudios\application\ActividadAsignaturaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadAsignaturaEliminar $useCase */
$useCase = DependencyResolver::get(ActividadAsignaturaEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
