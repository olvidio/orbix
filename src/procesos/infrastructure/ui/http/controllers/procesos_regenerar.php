<?php

use src\procesos\application\ProcesosRegenerar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_regenerar'),
        'procesos_regenerar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_tipo_proceso'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_proceso');

/** @var ProcesosRegenerar $useCase */
$useCase = DependencyResolver::get(ProcesosRegenerar::class);

ContestarJson::enviar($useCase->execute($input));
