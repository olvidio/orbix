<?php

/**
 * Quita el plano de una dirección. La dirección la fija la cápsula `ctx_plano_borrar`
 * que firma `DireccionesEditarData` (ficha de la dirección).
 */

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\ubis\application\PlanosDireccion;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_plano_borrar'),
        'plano_borrar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

try {
    DependencyResolver::get(PlanosDireccion::class)->borrar(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_dir'),
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_direccion')
    );
} catch (InvalidArgumentException $e) {
    ContestarJson::enviar($e->getMessage(), 'none');
    return;
}

ContestarJson::enviar('', 'ok');
