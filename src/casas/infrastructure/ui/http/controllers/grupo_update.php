<?php


/**
 * Endpoint backend: crea o actualiza un `GrupoCasa`.
 */

use src\casas\application\GrupoCasaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'grupo_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = [
    'id_item' => \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_item'),
    'id_ubi_padre' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi_padre'),
    'id_ubi_hijo' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi_hijo'),
];

/** @var GrupoCasaUpdate $useCase */
$useCase = DependencyResolver::get(GrupoCasaUpdate::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
