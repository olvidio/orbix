<?php

declare(strict_types=1);

use frontend\devel_db_admin\helpers\DevelDbAdminPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$modo = PayloadCoercion::string($_POST['modo'] ?? 'seleccion');
$seleccionados = DevelDbAdminPayload::migracionesSel($_POST['sel'] ?? []);
$prefijoHasta = PayloadCoercion::string($_POST['prefijo_hasta'] ?? '');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/migraciones_ejecutar', [
    'modo' => $modo,
    'sel' => $seleccionados,
    'prefijo_hasta' => $prefijoHasta,
]);

echo '<h1>' . _("resultado migraciones") . '</h1>';
$error = PayloadCoercion::string($data['error'] ?? '');
if ($error !== '') {
    echo '<p><strong>' . _("error") . ':</strong> '
        . htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        . '</p>';
}
echo '<pre>';
foreach (DevelDbAdminPayload::avisosList($data['lines'] ?? []) as $line) {
    echo htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "\n";
}
echo '</pre>';
