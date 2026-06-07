<?php
/**
 * Endpoint backend: elimina el sacd ({id_activ, id_cargo}) de una
 * actividad y la asistencia asociada. Responde JSON
 * `{success, mensaje, data}` via `ContestarJson::enviar`.
 */

use src\actividadessacd\application\SacdEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$input = [
    'id_activ' => input_int($_POST, 'id_activ'),
    'id_cargo' => input_int($_POST, 'id_cargo'),
    'id_nom' => input_int($_POST, 'id_nom'),
];

/** @var SacdEliminar $useCase */
$useCase = DependencyResolver::get(SacdEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
