<?php

use src\procesos\application\TipoActivProcesoAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_asignar'),
        'tipo_activ_proceso_asignar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_tipo_activ'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_tipo_activ');

/** @var TipoActivProcesoAsignar $useCase */
$useCase = DependencyResolver::get(TipoActivProcesoAsignar::class);

ContestarJson::enviar($useCase->execute($input));
