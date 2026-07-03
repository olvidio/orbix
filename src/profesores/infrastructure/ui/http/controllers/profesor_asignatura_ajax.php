<?php

use src\profesores\application\ProfesoresAsignaturaLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProfesoresAsignaturaLista $useCase */
$useCase = DependencyResolver::get(ProfesoresAsignaturaLista::class);
ContestarJson::enviar('', $useCase->getTablaData(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_asignatura')));
