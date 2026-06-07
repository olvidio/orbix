<?php

use function src\shared\domain\helpers\input_int;

use src\profesores\application\ProfesoresAsignaturaLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProfesoresAsignaturaLista $useCase */
$useCase = DependencyResolver::get(ProfesoresAsignaturaLista::class);
ContestarJson::enviar('', $useCase->getTablaData(input_int($_POST, 'id_asignatura')));
