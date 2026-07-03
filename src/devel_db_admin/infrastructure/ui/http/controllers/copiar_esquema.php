<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Ejecuta {@see CopiarEsquema} (POST: esquema, region, dl, comun, sv, sf). Respuesta JSON `data`: `ok` y `avisos`.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\CopiarEsquema;


$QEsquemaRef = (string) FilterPostGet::post('esquema');
$Qregion = (string) FilterPostGet::post('region');
$Qdl = (string) FilterPostGet::post('dl');
$Qcomun = (int) FilterPostGet::post('comun');
$Qsv = (int) FilterPostGet::post('sv');
$Qsf = (int) FilterPostGet::post('sf');

try {
    $avisos = (new CopiarEsquema())->ejecutar($QEsquemaRef, $Qregion, $Qdl, $Qcomun, $Qsv, $Qsf);
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

ContestarJson::enviar('', ['ok' => true, 'avisos' => $avisos]);
