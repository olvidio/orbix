<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Verificación de estado del renombre (POST: esquema_origen opcional para solo comprobar el destino; región y dl obligatorios; acepta POST esquema legado con sufijo v/f como origen).
 * Respuesta JSON `data`: objeto con listo, resumen, bloques, meta.
 */

use src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto;
use src\devel_db_admin\application\VerificarEstadoRenombrarEsquema;
use src\shared\web\ContestarJson;


$QEsquemaOrigen = trim((string) FilterPostGet::post('esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) FilterPostGet::post('esquema'));
}
$Qregion = (string) FilterPostGet::post('region');
$Qdl = (string) FilterPostGet::post('dl');
$Qcomun = (int) FilterPostGet::post('comun');
$Qsv = (int) FilterPostGet::post('sv');
$Qsf = (int) FilterPostGet::post('sf');

$payload = (new VerificarEstadoRenombrarEsquema())->ejecutar(
    $QEsquemaOrigen,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
    $Qsf,
);

ContestarJson::enviar('', $payload);
