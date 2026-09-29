<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * Endpoint backend para `actividad_nuevo_curso` (ejecucion).
 * Abre `ctx_ejecutar` (HashB acción-only) emitido por
 * `actividad_nuevo_curso_form_data`. Recibe year_ref, year y ver_lista via POST.
 */

use src\actividades\application\ActividadNuevoCursoEjecutar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_ejecutar'),
        'actividad_nuevo_curso_ejecutar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'year_ref' => (int)\src\shared\domain\helpers\FilterPostGet::post('year_ref'),
    'year' => (int)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'ver_lista' => (string)\src\shared\domain\helpers\FilterPostGet::post('ver_lista'),
];

/** @var ActividadNuevoCursoEjecutar $useCase */
$useCase = DependencyResolver::get(ActividadNuevoCursoEjecutar::class);
$data = $useCase->ejecutar($input);

ContestarJson::enviar('', $data);
