<?php

declare(strict_types=1);

/**
 * Ejecuta {@see EliminarEsquemaDl} (POST: region, dl, comun, sv, sf). Respuesta JSON `data`: `ok` y `avisos`.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\EliminarEsquemaDl;


$Qregion = (string) filter_post('region');
$Qdl = (string) filter_post('dl');
$Qcomun = (int) filter_post('comun');
$Qsv = (int) filter_post('sv');
$Qsf = (int) filter_post('sf');

try {
    $avisos = (new EliminarEsquemaDl())->ejecutar($Qregion, $Qdl, $Qcomun, $Qsv, $Qsf);
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

ContestarJson::enviar('', ['ok' => true, 'avisos' => $avisos]);
