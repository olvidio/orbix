<?php

use src\dbextern\application\CrearTodosDesdeListasUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_crear_todos'),
        'sincro_crear_todos'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$region = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'region');
$dl = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'dl');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'tipo_persona');

$result = DependencyResolver::get(CrearTodosDesdeListasUseCase::class)($region, $dl, $tipo_persona);

$error_txt = $result['errors'] !== [] ? implode("\n", $result['errors']) : '';
$data = ['count' => $result['count']];

ContestarJson::enviar($error_txt, $data);
