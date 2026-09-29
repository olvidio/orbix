<?php

use src\encargossacd\application\EncargoVerEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'encargo_ver_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['sel'] = [(string) \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_enc')];

/** @var EncargoVerEliminar $useCase */
$useCase = DependencyResolver::get(EncargoVerEliminar::class);

$result = $useCase->execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
