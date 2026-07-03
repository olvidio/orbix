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

$QEsquemaRef = (string) \src\shared\domain\helpers\FilterPostGet::post('esquema');
$Qregion = (string) \src\shared\domain\helpers\FilterPostGet::post('region');
$Qdl = (string) \src\shared\domain\helpers\FilterPostGet::post('dl');
$Qcomun = (int) \src\shared\domain\helpers\FilterPostGet::post('comun');
$Qsv = (int) \src\shared\domain\helpers\FilterPostGet::post('sv');
$Qsf = (int) \src\shared\domain\helpers\FilterPostGet::post('sf');

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
