<?php
/**
 * Endpoint backend: elimina un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$input = [
    'id_item' => input_int($_POST, 'id_item'),
];

/** @var GrupoCasaEliminar $useCase */
$useCase = DependencyResolver::get(GrupoCasaEliminar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
