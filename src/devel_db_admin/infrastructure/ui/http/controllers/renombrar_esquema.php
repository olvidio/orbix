<?php

declare(strict_types=1);

/**
 * Ejecuta {@see RenombrarEsquema} (POST: esquema, region, dl, comun, sv). Respuesta JSON `data`: `"ok"`.
 */

use frontend\shared\web\ContestarJson;
use src\devel_db_admin\application\RenombrarEsquema;

require_once 'frontend/shared/global_header_front.inc';

$QEsquemaRef = (string) filter_input(INPUT_POST, 'esquema');
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');

(new RenombrarEsquema($GLOBALS['container']))->ejecutar(
    $QEsquemaRef,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
);

ContestarJson::enviar('', 'ok');
