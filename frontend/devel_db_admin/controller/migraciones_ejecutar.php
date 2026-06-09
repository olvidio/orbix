<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$modo = (string) ($_POST['modo'] ?? 'seleccion');
$seleccionados = $_POST['sel'] ?? [];
if (!is_array($seleccionados)) {
    $seleccionados = [$seleccionados];
}
$seleccionados = array_values(array_filter(array_map('strval', $seleccionados), static fn (string $value): bool => $value !== ''));
$prefijoHasta = (string) ($_POST['prefijo_hasta'] ?? '');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/migraciones_ejecutar', [
    'modo' => $modo,
    'sel' => $seleccionados,
    'prefijo_hasta' => $prefijoHasta,
]);
$data = is_array($data) ? $data : [];

echo '<h1>' . _("resultado migraciones") . '</h1>';
if (!empty($data['error'])) {
    echo '<p><strong>' . _("error") . ':</strong> '
        . htmlspecialchars((string) $data['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        . '</p>';
}
echo '<pre>';
foreach ((array) ($data['lines'] ?? []) as $line) {
    echo htmlspecialchars((string) $line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\n";
}
echo '</pre>';
