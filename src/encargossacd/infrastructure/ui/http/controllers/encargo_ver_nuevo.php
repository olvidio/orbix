<?php

use src\encargossacd\application\EncargoVerNuevo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_nuevo'),
        'encargo_ver_nuevo'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var EncargoVerNuevo $useCase */
$useCase = DependencyResolver::get(EncargoVerNuevo::class);


$result = $useCase->execute($_POST);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
