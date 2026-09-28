<?php

use src\dbextern\application\RefrescarBduUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_refrescar'),
        'refrescar_bdu'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = '';
try {
    DependencyResolver::get(RefrescarBduUseCase::class)();
} catch (Exception $e) {
    $error_txt = _("Error al refrescar la BDU") . ": " . $e->getMessage();
}

ContestarJson::enviar($error_txt, 'ok');
