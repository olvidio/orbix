<?php

declare(strict_types=1);

/**
 * Ejecuta {@see CopiarEsquema} (POST: esquema, region, dl, comun, sv, sf). Respuesta JSON `data`: `ok` y `avisos`.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\CopiarEsquema;

require_once 'frontend/shared/global_header_front.inc';

$QEsquemaRef = (string) filter_input(INPUT_POST, 'esquema');
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

try {
    $avisos = (new CopiarEsquema())->ejecutar($QEsquemaRef, $Qregion, $Qdl, $Qcomun, $Qsv, $Qsf);
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

ContestarJson::enviar('', ['ok' => true, 'avisos' => $avisos]);
