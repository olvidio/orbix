<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * POST: esquema_origen opcional (vacío = solo defaults sobre destino); región y dl obligatorios; acepta POST esquema legado como origen.
 * Respuesta JSON `data`: acciones, avisos, verificacion (resultado de volver a comprobar).
 */

use src\devel_db_admin\application\CorregirEstadoRenombrarEsquema;
use src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


/** @var CorregirEstadoRenombrarEsquema $useCase */
$useCase = DependencyResolver::get(CorregirEstadoRenombrarEsquema::class);

$QEsquemaOrigen = trim((string) FilterPostGet::post('esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) FilterPostGet::post('esquema'));
}
$Qregion = (string) FilterPostGet::post('region');
$Qdl = (string) FilterPostGet::post('dl');
$Qcomun = (int) FilterPostGet::post('comun');
$Qsv = (int) FilterPostGet::post('sv');
$Qsf = (int) FilterPostGet::post('sf');

$payload = $useCase->ejecutar(
    $QEsquemaOrigen,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
    $Qsf,
);

ContestarJson::enviar('', $payload);
