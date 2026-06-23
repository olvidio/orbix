<?php

declare(strict_types=1);

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

$QEsquemaOrigen = trim((string) filter_post('esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) filter_post('esquema'));
}
$Qregion = (string) filter_post('region');
$Qdl = (string) filter_post('dl');
$Qcomun = (int) filter_post('comun');
$Qsv = (int) filter_post('sv');
$Qsf = (int) filter_post('sf');

$payload = $useCase->ejecutar(
    $QEsquemaOrigen,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
    $Qsf,
);

ContestarJson::enviar('', $payload);
