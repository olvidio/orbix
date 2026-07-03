<?php

use src\profesores\application\ProfesoresAsignaturaLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var ProfesoresAsignaturaLista $useCase */
$useCase = DependencyResolver::get(ProfesoresAsignaturaLista::class);
ContestarJson::enviar('', $useCase->getTablaData(FuncTablasSupport::inputInt($_POST, 'id_asignatura')));
