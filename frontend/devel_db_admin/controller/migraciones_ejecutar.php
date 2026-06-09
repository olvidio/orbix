<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/devel_db_admin/helpers/devel_db_admin_support.php';

FrontBootstrap::boot();
$modo = tessera_imprimir_string($_POST['modo'] ?? 'seleccion');
$seleccionados = devel_db_admin_migraciones_sel($_POST['sel'] ?? []);
$prefijoHasta = tessera_imprimir_string($_POST['prefijo_hasta'] ?? '');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/migraciones_ejecutar', [
    'modo' => $modo,
    'sel' => $seleccionados,
    'prefijo_hasta' => $prefijoHasta,
]);

echo '<h1>' . _("resultado migraciones") . '</h1>';
$error = tessera_imprimir_string($data['error'] ?? '');
if ($error !== '') {
    echo '<p><strong>' . _("error") . ':</strong> '
        . htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        . '</p>';
}
echo '<pre>';
foreach (devel_db_admin_avisos_list($data['lines'] ?? []) as $line) {
    echo htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\n";
}
echo '</pre>';
