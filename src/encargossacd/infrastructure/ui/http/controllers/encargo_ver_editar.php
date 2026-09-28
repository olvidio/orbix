<?php

use src\encargossacd\application\EncargoVerEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_editar'),
        'encargo_ver_editar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['id_enc'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_enc');

/** @var EncargoVerEditar $useCase */
$useCase = DependencyResolver::get(EncargoVerEditar::class);


$result = $useCase->execute($input);
ContestarJson::enviar($result['error'], $result['error'] === '' ? 'ok' : 'none');
