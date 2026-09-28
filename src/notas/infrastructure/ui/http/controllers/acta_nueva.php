<?php

use src\notas\application\ActaNueva;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_nuevo'),
        'acta_nueva'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = (DependencyResolver::get(ActaNueva::class))->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
