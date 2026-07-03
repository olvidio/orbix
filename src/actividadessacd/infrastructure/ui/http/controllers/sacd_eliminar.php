<?php


/**
 * Endpoint backend: elimina el sacd ({id_activ, id_cargo}) de una
 * actividad y la asistencia asociada. Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ'),
    'id_cargo' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_cargo'),
    'id_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom'),
];

/** @var SacdEliminar $useCase */
$useCase = DependencyResolver::get(SacdEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
