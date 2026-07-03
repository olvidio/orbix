<?php


/**
 * Endpoint backend: reordena sacd encargados (+/- prioridad). Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdReordenar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom'),
    'num_orden' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'num_orden'),
];

/** @var SacdReordenar $useCase */
$useCase = DependencyResolver::get(SacdReordenar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
