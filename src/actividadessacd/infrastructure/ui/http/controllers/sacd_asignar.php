<?php
/**
 * Endpoint backend: asigna un sacd a una actividad (y, si es sv, tambien
 * crea la asistencia). Responde JSON `{success, mensaje, data}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
    'id_nom' => input_int($_POST, 'id_nom'),
];

/** @var SacdAsignar $useCase */
$useCase = DependencyResolver::get(SacdAsignar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
