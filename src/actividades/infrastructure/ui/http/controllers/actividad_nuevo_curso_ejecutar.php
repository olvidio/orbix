<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend para `actividad_nuevo_curso` (ejecucion).
 * Recibe year_ref, year y ver_lista via POST y delega en
 * ActividadNuevoCursoEjecutar. Responde JSON (patron refactor.md).
 */

use src\actividades\application\ActividadNuevoCursoEjecutar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'year_ref' => (int)\src\shared\domain\helpers\FilterPostGet::post('year_ref'),
    'year' => (int)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'ver_lista' => (string)\src\shared\domain\helpers\FilterPostGet::post('ver_lista'),
];

/** @var ActividadNuevoCursoEjecutar $useCase */
$useCase = DependencyResolver::get(ActividadNuevoCursoEjecutar::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
