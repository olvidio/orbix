<?php
/**
 * Endpoint backend para `actividad_nuevo_curso` (ejecucion).
 * Recibe year_ref, year y ver_lista via POST y delega en
 * ActividadNuevoCursoEjecutar. Responde JSON (patron refactor.md).
 */

use src\actividades\application\ActividadNuevoCursoEjecutar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'year_ref' => (int)filter_post('year_ref'),
    'year' => (int)filter_post('year'),
    'ver_lista' => (string)filter_post('ver_lista'),
];

/** @var ActividadNuevoCursoEjecutar $useCase */
$useCase = DependencyResolver::get(ActividadNuevoCursoEjecutar::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
