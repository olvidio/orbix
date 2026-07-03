<?php


/**
 * Endpoint backend: incorpora las primeras peticiones de plaza de
 * cada persona como asistencia con plaza asignada/pedida (segun si
 * la actividad es de midele o de otra dl).
 */

use src\actividadplazas\application\PeticionesIncorporar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'sactividad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sactividad'),
    'sasistentes' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sasistentes'),
];

/** @var PeticionesIncorporar $useCase */
$useCase = DependencyResolver::get(PeticionesIncorporar::class);
$result = $useCase->execute($input);
$error = $result['error'];
unset($result['error']);
ContestarJson::enviar($error, $result);
