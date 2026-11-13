<?php

use src\profesores\application\ProfesoresAsignaturaLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ProfesoresAsignaturaLista $useCase */
$useCase = DependencyResolver::get(ProfesoresAsignaturaLista::class);
$Qid_asignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_asignatura');
if ($Qid_asignatura <= 0) {
    ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'id_asignatura'), 'none');
    return;
}
ContestarJson::enviar('', $useCase->getTablaData($Qid_asignatura));
