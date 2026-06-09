<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/devel_db_admin/helpers/devel_db_admin_support.php';

FrontBootstrap::boot();
$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/crear_usuarios', [
    'region' => $Qregion,
    'dl' => $Qdl,
]);

$archivo_conf = '  (comun.inc, sv.inc, sf.inc)';
echo sprintf(_("se han creado los usuarios. Ojo, un único usuario para pruebas y producción"));
echo '<br>';
echo sprintf(_("debe copiar los siguientes usuarios y passwords en el archivo %s"), $archivo_conf);
echo '<br>';
echo '<br>';
echo tessera_imprimir_string($data['esquema'] ?? '') . ' > ' . htmlspecialchars(tessera_imprimir_string($data['esquemaPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo tessera_imprimir_string($data['esquemav'] ?? '') . ' > ' . htmlspecialchars(tessera_imprimir_string($data['esquemavPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo tessera_imprimir_string($data['esquemaf'] ?? '') . ' > ' . htmlspecialchars(tessera_imprimir_string($data['esquemafPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo '<br>';
echo _("Ya no hace falta, pero interesa saberlo para acceder al a BD directamente.");
