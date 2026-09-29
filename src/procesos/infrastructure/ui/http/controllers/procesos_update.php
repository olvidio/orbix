<?php

use src\procesos\application\ProcesosUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_update'),
        'procesos_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_item'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');
$input['id_tipo_proceso'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_proceso');

/** @var ProcesosUpdate $useCase */
$useCase = DependencyResolver::get(ProcesosUpdate::class);

ContestarJson::enviar($useCase->execute($input));
