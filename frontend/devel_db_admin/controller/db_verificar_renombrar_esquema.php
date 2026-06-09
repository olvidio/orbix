<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$QEsquemaOrigen = trim((string) filter_input(INPUT_POST, 'esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = \src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) filter_input(INPUT_POST, 'esquema'));
}
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/verificar_renombrar_esquema', [
    'esquema_origen' => $QEsquemaOrigen,
    'region' => $Qregion,
    'dl' => $Qdl,
    'comun' => $Qcomun,
    'sv' => $Qsv,
    'sf' => $Qsf,
]);

header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
