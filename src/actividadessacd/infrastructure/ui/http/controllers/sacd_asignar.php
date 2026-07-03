<?php


/**
 * Endpoint backend: asigna un sacd a una actividad (y, si es sv, tambien
 * crea la asistencia). Responde JSON `{success, mensaje, data}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom'),
];

/** @var SacdAsignar $useCase */
$useCase = DependencyResolver::get(SacdAsignar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
