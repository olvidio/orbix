<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
// Si no llega `esquema_origen`, el endpoint lo deduce de `esquema` (nombre con sufijo v/f).
$QEsquemaOrigen = trim((string) filter_input(INPUT_POST, 'esquema_origen'));
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

PostRequest::getDataFromUrl('/src/devel_db_admin/renombrar_esquema', [
    'esquema_origen' => $QEsquemaOrigen,
    'esquema' => (string) filter_input(INPUT_POST, 'esquema'),
    'region' => $Qregion,
    'dl' => $Qdl,
    'comun' => $Qcomun,
    'sv' => $Qsv,
    'sf' => $Qsf,
    'ctx_renombrar' => (string) filter_input(INPUT_POST, 'ctx_renombrar'),
]);

echo '<br>';
echo sprintf(_("se ha cambiado el nombre del esquema"));
