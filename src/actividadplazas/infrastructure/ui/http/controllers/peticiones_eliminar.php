<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: elimina todas las peticiones de una
 * persona+tipo.
 */

use src\actividadplazas\application\PeticionesEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_nom' => FuncTablasSupport::inputInt($_POST, 'id_nom'),
    'sactividad' => FuncTablasSupport::inputString($_POST, 'sactividad'),
];

/** @var PeticionesEliminar $useCase */
$useCase = DependencyResolver::get(PeticionesEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
