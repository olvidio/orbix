<?php

use src\profesores\application\ProfesorAsignaturaQueData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProfesorAsignaturaQueData $useCase */
$useCase = DependencyResolver::get(ProfesorAsignaturaQueData::class);
ContestarJson::enviar('', $useCase->execute($_POST));
