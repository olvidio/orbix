<?php

use src\actividadestudios\application\ProfesoresDesplegableData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProfesoresDesplegableData $useCase */
$useCase = DependencyResolver::get(ProfesoresDesplegableData::class);
$data = $useCase->execute($_POST);
ContestarJson::enviar('', $data);
