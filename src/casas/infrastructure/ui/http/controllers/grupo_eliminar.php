<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: elimina un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => FuncTablasSupport::inputInt($_POST, 'id_item'),
];

/** @var GrupoCasaEliminar $useCase */
$useCase = DependencyResolver::get(GrupoCasaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
