<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesQuitarRegistro;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;


$capsules = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'ctx_quitar');
$seleccionados = [];
foreach ($capsules as $capsule) {
    try {
        $ctx = HashB::open((string) $capsule, 'migraciones_quitar_registro');
    } catch (HashBInvalidException $e) {
        continue;
    }
    $id = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id');
    if ($id !== '') {
        $seleccionados[] = $id;
    }
}
if ($capsules === [] || $seleccionados === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var MigracionAplicadaRepositoryInterface $repo */
$repo = DependencyResolver::get(MigracionAplicadaRepositoryInterface::class);

$result = (new MigracionesQuitarRegistro($repo))->quitar($seleccionados);

ContestarJson::enviar('', [
    'lines' => $result['lines'],
    'error' => $result['error'],
]);
