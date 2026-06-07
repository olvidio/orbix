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
use function src\shared\domain\helpers\input_string;

$input = ['f_ini_iso' => input_string($_POST, 'f_ini_iso')];

/** @var SacdAsignarAuto $useCase */
$useCase = DependencyResolver::get(SacdAsignarAuto::class);
ContestarJson::enviar('', $useCase->execute($input));
