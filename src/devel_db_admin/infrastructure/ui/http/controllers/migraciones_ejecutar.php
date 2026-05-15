<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesEjecutar;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$modo = (string) ($_POST['modo'] ?? 'seleccion');
$seleccionados = $_POST['sel'] ?? [];
if (!is_array($seleccionados)) {
    $seleccionados = [$seleccionados];
}
$seleccionados = array_values(array_filter(array_map('strval', $seleccionados), static fn (string $value): bool => $value !== ''));
$prefijoHasta = (string) ($_POST['prefijo_hasta'] ?? '');

$result = (new MigracionesEjecutar($GLOBALS['container']))->ejecutar($modo, $seleccionados, $prefijoHasta);

ContestarJson::enviar('', [
    'lines' => $result['lines'] ?? [],
    'error' => $result['error'] ?? null,
]);
