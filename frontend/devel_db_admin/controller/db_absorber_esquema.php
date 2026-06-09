<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/devel_db_admin/helpers/devel_db_admin_support.php';

FrontBootstrap::boot();
$QEsquemaMatriz = (string) filter_input(INPUT_POST, 'esquema_matriz');
$QEsquemaDel = (string) filter_input(INPUT_POST, 'esquema_del');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/absorber_esquema', [
    'esquema_matriz' => $QEsquemaMatriz,
    'esquema_del' => $QEsquemaDel,
]);

foreach (devel_db_admin_avisos_list($data['lines'] ?? []) as $line) {
    echo '<br>' . htmlspecialchars($line) . '<br>';
}

$errores = devel_db_admin_avisos_list($data['errores'] ?? []);
if ($errores !== []) {
    echo '<br><strong>' . htmlspecialchars(_('Errores durante la absorción')) . ':</strong><br>';
    foreach ($errores as $error) {
        echo '<br>' . htmlspecialchars($error) . '<br>';
    }
}
