<?php

declare(strict_types=1);

/**
 * Verificación de estado del renombre (POST: esquema_origen opcional para solo comprobar el destino; región y dl obligatorios; acepta POST esquema legado con sufijo v/f como origen).
 * Respuesta JSON `data`: objeto con listo, resumen, bloques, meta.
 */

use src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto;
use src\devel_db_admin\application\VerificarEstadoRenombrarEsquema;
use src\shared\web\ContestarJson;


$QEsquemaOrigen = trim((string) filter_post('esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) filter_post('esquema'));
}
$Qregion = (string) filter_post('region');
$Qdl = (string) filter_post('dl');
$Qcomun = (int) filter_post('comun');
$Qsv = (int) filter_post('sv');
$Qsf = (int) filter_post('sf');

$payload = (new VerificarEstadoRenombrarEsquema())->ejecutar(
    $QEsquemaOrigen,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
    $Qsf,
);

ContestarJson::enviar('', $payload);
