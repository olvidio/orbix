<?php

/**
 * HTML de {@see frontend/notas/controller/comprobar_notas.php} (SQL en backend).
 */

use frontend\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$oDB = $GLOBALS['oDB'] ?? null;
if (!$oDB instanceof \PDO) {
    ContestarJson::enviar(_('No hay conexión a base de datos (comprobar_notas).'));
    return;
}

ob_start();
try {
    require __DIR__ . '/comprobar_notas_page_body.inc.php';
} catch (\Throwable $e) {
    ob_end_clean();
    ContestarJson::enviar($e->getMessage());

    return;
}
$html = ob_get_clean();

ContestarJson::enviar('', ['html' => $html]);
