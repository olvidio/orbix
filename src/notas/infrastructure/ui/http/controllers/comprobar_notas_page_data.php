<?php
use src\shared\infrastructure\GlobalPdo;

/**
 * HTML de {@see frontend/notas/controller/comprobar_notas.php} (SQL en backend).
 */

use src\shared\web\ContestarJson;


$oDB = GlobalPdo::get('oDB');

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
