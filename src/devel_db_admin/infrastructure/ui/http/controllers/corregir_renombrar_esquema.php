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

require_once 'frontend/shared/global_header_front.inc';

/** @var CorregirEstadoRenombrarEsquema $useCase */
$useCase = DependencyResolver::get(CorregirEstadoRenombrarEsquema::class);

$QEsquemaOrigen = trim((string) filter_input(INPUT_POST, 'esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) filter_input(INPUT_POST, 'esquema'));
}
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

$payload = $useCase->ejecutar(
    $QEsquemaOrigen,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
    $Qsf,
);

ContestarJson::enviar('', $payload);
