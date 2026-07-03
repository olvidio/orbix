<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Ejecuta {@see CopiarEsquema} (POST: esquema, region, dl, comun, sv, sf). Respuesta JSON `data`: `ok` y `avisos`.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\CopiarEsquema;


$QEsquemaRef = (string) \src\shared\domain\helpers\FilterPostGet::post('esquema');
$Qregion = (string) \src\shared\domain\helpers\FilterPostGet::post('region');
$Qdl = (string) \src\shared\domain\helpers\FilterPostGet::post('dl');
$Qcomun = (int) \src\shared\domain\helpers\FilterPostGet::post('comun');
$Qsv = (int) \src\shared\domain\helpers\FilterPostGet::post('sv');
$Qsf = (int) \src\shared\domain\helpers\FilterPostGet::post('sf');

try {
    $avisos = (new CopiarEsquema())->ejecutar($QEsquemaRef, $Qregion, $Qdl, $Qcomun, $Qsv, $Qsf);
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

ContestarJson::enviar('', ['ok' => true, 'avisos' => $avisos]);
