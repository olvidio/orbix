<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: crea o actualiza un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_item' => FuncTablasSupport::inputString($_POST, 'id_item'),
    'id_ubi_padre' => FuncTablasSupport::inputInt($_POST, 'id_ubi_padre'),
    'id_ubi_hijo' => FuncTablasSupport::inputInt($_POST, 'id_ubi_hijo'),
];

/** @var GrupoCasaUpdate $useCase */
$useCase = DependencyResolver::get(GrupoCasaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
