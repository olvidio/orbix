<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: reordena sacd encargados (+/- prioridad). Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdReordenar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'id_nom' => FuncTablasSupport::inputInt($_POST, 'id_nom'),
    'num_orden' => FuncTablasSupport::inputString($_POST, 'num_orden'),
];

/** @var SacdReordenar $useCase */
$useCase = DependencyResolver::get(SacdReordenar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
