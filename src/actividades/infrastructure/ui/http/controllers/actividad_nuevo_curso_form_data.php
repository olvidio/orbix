<?php

/**
 * Endpoint backend: emite `ctx_ejecutar` (HashB acción-only) para
 * `actividad_nuevo_curso_ejecutar`.
 */

use src\actividades\application\ActividadNuevoCursoFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ActividadNuevoCursoFormData $useCase */
$useCase = DependencyResolver::get(ActividadNuevoCursoFormData::class);
ContestarJson::enviar('', $useCase->execute());
