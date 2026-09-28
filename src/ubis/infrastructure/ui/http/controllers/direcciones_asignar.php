<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesAsignar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_asignar'),
        'direcciones_asignar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

ContestarJson::enviar('', DependencyResolver::get(DireccionesAsignar::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_dir'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_direccion')
));
