<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/devel_db_admin/helpers/devel_db_admin_support.php';

FrontBootstrap::boot();
$QEsquemaRef = (string) filter_input(INPUT_POST, 'esquema');
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');
$Qcomun = (int) filter_input(INPUT_POST, 'comun');
$Qsv = (int) filter_input(INPUT_POST, 'sv');
$Qsf = (int) filter_input(INPUT_POST, 'sf');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/crear_esquema', [
    'esquema' => $QEsquemaRef,
    'region' => $Qregion,
    'dl' => $Qdl,
    'comun' => $Qcomun,
    'sv' => $Qsv,
    'sf' => $Qsf,
]);

$avisos = devel_db_admin_avisos_list($data['avisos'] ?? []);
if (($data['ok'] ?? true) === false) {
    echo '<br>';
    echo '<strong>' . _('Avisos') . ':</strong><ul>';
    foreach ($avisos as $aviso) {
        echo '<li>' . htmlspecialchars($aviso, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
    return;
}

echo '<br>';
echo sprintf(_("se ha creado la estructura de los esquemas."));
if ($avisos !== []) {
    echo '<br><strong>' . _('Avisos') . ':</strong><ul>';
    foreach ($avisos as $aviso) {
        echo '<li>' . htmlspecialchars($aviso, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
}
