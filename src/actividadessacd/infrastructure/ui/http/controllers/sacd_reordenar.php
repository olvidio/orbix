<?php
/**
 * Endpoint backend: reordena sacd encargados (+/- prioridad). Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdReordenar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
    'id_nom' => input_int($_POST, 'id_nom'),
    'num_orden' => input_string($_POST, 'num_orden'),
];

/** @var SacdReordenar $useCase */
$useCase = DependencyResolver::get(SacdReordenar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
