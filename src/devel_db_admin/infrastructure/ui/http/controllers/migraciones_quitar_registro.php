<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesQuitarRegistro;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var MigracionAplicadaRepositoryInterface $repo */
$repo = DependencyResolver::get(MigracionAplicadaRepositoryInterface::class);

$seleccionados = $_POST['sel'] ?? [];
if (!is_array($seleccionados)) {
    $seleccionados = [$seleccionados];
}
$filtrados = [];
foreach ($seleccionados as $seleccionado) {
    if (is_scalar($seleccionado) && (string) $seleccionado !== '') {
        $filtrados[] = (string) $seleccionado;
    }
}
$seleccionados = $filtrados;

$result = (new MigracionesQuitarRegistro($repo))->quitar($seleccionados);

ContestarJson::enviar('', [
    'lines' => $result['lines'],
    'error' => $result['error'],
]);
