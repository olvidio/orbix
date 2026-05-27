<?php

declare(strict_types=1);

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$QEsquemaMatriz = (string) filter_input(INPUT_POST, 'esquema_matriz');
$QEsquemaDel = (string) filter_input(INPUT_POST, 'esquema_del');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/absorber_esquema', [
    'esquema_matriz' => $QEsquemaMatriz,
    'esquema_del' => $QEsquemaDel,
]);
$data = is_array($data) ? $data : [];
$lines = (array) ($data['lines'] ?? []);
$errores = (array) ($data['errores'] ?? []);

foreach ($lines as $line) {
    echo '<br>' . htmlspecialchars((string) $line) . '<br>';
}

if ($errores !== []) {
    echo '<br><strong>' . htmlspecialchars(_('Errores durante la absorción')) . ':</strong><br>';
    foreach ($errores as $error) {
        echo '<br>' . htmlspecialchars((string) $error) . '<br>';
    }
}
