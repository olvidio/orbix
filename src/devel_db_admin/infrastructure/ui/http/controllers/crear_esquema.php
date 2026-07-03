<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Ejecuta {@see CrearEsquema} (POST: esquema, region, dl, comun, sv, sf). Respuesta JSON `data`: `"ok"`.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\CrearEsquema;
use src\devel_db_admin\application\CrearEsquemaPrecondicionException;
use src\shared\infrastructure\DependencyResolver;


/** @var CrearEsquema $useCase */
$useCase = DependencyResolver::get(CrearEsquema::class);

$QEsquemaRef = (string) FilterPostGet::post('esquema');
$Qregion = (string) FilterPostGet::post('region');
$Qdl = (string) FilterPostGet::post('dl');
$Qcomun = (int) FilterPostGet::post('comun');
$Qsv = (int) FilterPostGet::post('sv');
$Qsf = (int) FilterPostGet::post('sf');

try {
    $avisos = $useCase->ejecutar(
        $QEsquemaRef,
        $Qregion,
        $Qdl,
        $Qcomun,
        $Qsv,
        $Qsf,
    );
} catch (CrearEsquemaPrecondicionException $e) {
    ContestarJson::enviar('', ['ok' => false, 'avisos' => [$e->getMessage()]]);
    return;
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

ContestarJson::enviar('', ['ok' => true, 'avisos' => $avisos]);
