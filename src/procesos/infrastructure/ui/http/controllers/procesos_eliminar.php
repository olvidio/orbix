<?php

use src\procesos\application\ProcesosEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'procesos_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_item'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');

/** @var ProcesosEliminar $useCase */
$useCase = DependencyResolver::get(ProcesosEliminar::class);

ContestarJson::enviar($useCase->execute($input));
