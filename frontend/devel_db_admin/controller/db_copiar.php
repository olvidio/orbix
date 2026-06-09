<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$QEsquemaRef = (string) filter_input(INPUT_POST, 'esquema');
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

$esquema = "$Qregion-$Qdl";

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/copiar_esquema', [
    'esquema' => $QEsquemaRef,
    'region' => $Qregion,
    'dl' => $Qdl,
    'comun' => $Qcomun,
    'sv' => $Qsv,
    'sf' => $Qsf,
]);

echo '<br>';
echo sprintf(_("esquema: %s. Se han pasado los datos que se tenían (según bloques aplicables)."), $esquema);

$avisos = $data['avisos'] ?? [];
if (is_array($avisos) && $avisos !== []) {
    echo '<br><strong>' . _('Avisos') . ':</strong><ul>';
    foreach ($avisos as $aviso) {
        echo '<li>' . htmlspecialchars((string) $aviso, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
}
