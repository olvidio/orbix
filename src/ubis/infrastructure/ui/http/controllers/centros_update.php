<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\ubis\application\CentrosUpdate;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'centros_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_ubi'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi');

/** @var CentrosUpdate $useCase */
$useCase = DependencyResolver::get(CentrosUpdate::class);

$error = $useCase->execute($input);
ContestarJson::enviar($error, 'ok');
