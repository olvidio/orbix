<?php


/**
 * Endpoint backend: auto-asignacion masiva del sacd titular del centro
 * encargado a actividades sr/sg sin sacd. Responde JSON
 * `{success, mensaje, data: {asignadas, sin_asignar}}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdAsignarAuto;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = ['f_ini_iso' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_ini_iso')];

/** @var SacdAsignarAuto $useCase */
$useCase = DependencyResolver::get(SacdAsignarAuto::class);
ContestarJson::enviar('', $useCase->execute($input));
