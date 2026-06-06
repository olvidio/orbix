<?php
/**
 * Endpoint backend: crea o actualiza un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_item' => input_string($_POST, 'id_item'),
    'id_ubi_padre' => input_int($_POST, 'id_ubi_padre'),
    'id_ubi_hijo' => input_int($_POST, 'id_ubi_hijo'),
];

/** @var GrupoCasaUpdate $useCase */
$useCase = DependencyResolver::get(GrupoCasaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
