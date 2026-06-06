<?php
/**
 * Endpoint backend: elimina todas las peticiones de una
 * persona+tipo.
 */

use src\actividadplazas\application\PeticionesEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_nom' => input_int($_POST, 'id_nom'),
    'sactividad' => input_string($_POST, 'sactividad'),
];

/** @var PeticionesEliminar $useCase */
$useCase = DependencyResolver::get(PeticionesEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
