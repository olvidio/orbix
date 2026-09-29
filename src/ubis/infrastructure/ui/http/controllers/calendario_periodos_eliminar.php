<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodoEliminar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'calendario_periodo_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

ContestarJson::enviar(
    DependencyResolver::get(CalendarioPeriodoEliminar::class)->execute(
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item')
    ),
    'ok'
);
