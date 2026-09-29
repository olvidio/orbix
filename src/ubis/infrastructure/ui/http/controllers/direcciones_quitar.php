<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesQuitar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_quitar'),
        'direcciones_quitar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

ContestarJson::enviar('', DependencyResolver::get(DireccionesQuitar::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi'),
    (int) ($ctx['idx'] ?? 0),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_dir'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_direccion')
));
