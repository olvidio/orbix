<?php

use src\notas\application\ActaModificar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'acta_modificar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['acta'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'acta');
$input['sel'] = [];

$error_txt = (DependencyResolver::get(ActaModificar::class))->execute($input);
ContestarJson::enviar($error_txt, 'ok');
