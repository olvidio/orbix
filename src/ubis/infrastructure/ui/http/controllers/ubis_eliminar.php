<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisEliminar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'ubis_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var UbisEliminar $useCase */
$useCase = DependencyResolver::get(UbisEliminar::class);
$errorTxt = $useCase->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi')
);
ContestarJson::enviar($errorTxt, 'ok');
