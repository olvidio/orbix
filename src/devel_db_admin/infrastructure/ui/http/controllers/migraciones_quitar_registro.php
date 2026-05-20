<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesQuitarRegistro;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$seleccionados = $_POST['sel'] ?? [];
if (!is_array($seleccionados)) {
    $seleccionados = [$seleccionados];
}
$seleccionados = array_values(array_filter(array_map('strval', $seleccionados), static fn (string $value): bool => $value !== ''));

$repo = $GLOBALS['container']->get(MigracionAplicadaRepositoryInterface::class);
$result = (new MigracionesQuitarRegistro($repo))->quitar($seleccionados);

ContestarJson::enviar('', [
    'lines' => $result['lines'] ?? [],
    'error' => $result['error'] ?? null,
]);
