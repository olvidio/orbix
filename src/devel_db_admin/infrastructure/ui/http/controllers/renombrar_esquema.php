<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Ejecuta {@see RenombrarEsquema} (POST: esquema_origen, region, dl, comun, sv, sf; esquema legado con sufijo v/f se acepta como origen). Respuesta JSON: `data` es JSON con `ok` y `avisos` (textos informativos, p. ej. rol destino reemplazado).
 */

use src\devel_db_admin\application\RenombrarEsquema;
use src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


/** @var RenombrarEsquema $useCase */
$useCase = DependencyResolver::get(RenombrarEsquema::class);

$QEsquemaOrigen = trim((string) FilterPostGet::post('esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) FilterPostGet::post('esquema'));
}
$Qregion = (string) FilterPostGet::post('region');
$Qdl = (string) FilterPostGet::post('dl');
$Qcomun = (int) FilterPostGet::post('comun');
$Qsv = (int) FilterPostGet::post('sv');
$Qsf = (int) FilterPostGet::post('sf');

try {
    $payload = $useCase->ejecutar(
        $QEsquemaOrigen,
        $Qregion,
        $Qdl,
        $Qcomun,
        $Qsv,
        $Qsf,
    );
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

if (!empty($payload['error'])) {
    ContestarJson::enviar((string) $payload['error'], 'none', 200);
    return;
}

ContestarJson::enviar('', ['ok' => true, 'avisos' => $payload['avisos']]);
