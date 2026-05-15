<?php

declare(strict_types=1);

/**
 * Verificación de estado del renombre (POST: esquema_origen opcional para solo comprobar el destino; región y dl obligatorios; acepta POST esquema legado con sufijo v/f como origen).
 * Respuesta JSON `data`: objeto con listo, resumen, bloques, meta.
 */

use src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto;
use src\devel_db_admin\application\VerificarEstadoRenombrarEsquema;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$QEsquemaOrigen = trim((string) filter_input(INPUT_POST, 'esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) filter_input(INPUT_POST, 'esquema'));
}
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

$payload = (new VerificarEstadoRenombrarEsquema())->ejecutar(
    $QEsquemaOrigen,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
    $Qsf,
);

ContestarJson::enviar('', $payload);
