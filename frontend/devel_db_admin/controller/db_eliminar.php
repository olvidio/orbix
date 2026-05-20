<?php

declare(strict_types=1);

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/eliminar_esquema', [
    'region' => $Qregion,
    'dl' => $Qdl,
    'comun' => $Qcomun,
    'sv' => $Qsv,
    'sf' => $Qsf,
]);

echo _('Datos pasados a resto (según bloques aplicables), esquemas eliminados y roles borrados en las bases marcadas.');

$avisos = $data['avisos'] ?? [];
if (is_array($avisos) && $avisos !== []) {
    echo '<br><strong>' . _('Avisos') . ':</strong><ul>';
    foreach ($avisos as $aviso) {
        echo '<li>' . htmlspecialchars((string) $aviso, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
}
