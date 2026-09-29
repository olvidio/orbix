<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodoGuardar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'calendario_periodo_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

ContestarJson::enviar(
    DependencyResolver::get(CalendarioPeriodoGuardar::class)->execute(
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item'),
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi'),
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_ini'),
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_fin'),
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'sfsv')
    ),
    'ok'
);
