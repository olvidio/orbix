<?php

declare(strict_types=1);

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$Qregion = (string) filter_input(INPUT_POST, 'region');
$Qdl = (string) filter_input(INPUT_POST, 'dl');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/crear_usuarios', [
    'region' => $Qregion,
    'dl' => $Qdl,
]);
$data = is_array($data) ? $data : [];

$archivo_conf = '  (comun.inc, sv.inc, sf.inc)';
echo sprintf(_("se han creado los usuarios. Ojo, un único usuario para pruebas y producción"));
echo '<br>';
echo sprintf(_("debe copiar los siguientes usuarios y passwords en el archivo %s"), $archivo_conf);
echo '<br>';
echo '<br>';
echo ($data['esquema'] ?? '') . ' > ' . htmlspecialchars((string) ($data['esquemaPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo ($data['esquemav'] ?? '') . ' > ' . htmlspecialchars((string) ($data['esquemavPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo ($data['esquemaf'] ?? '') . ' > ' . htmlspecialchars((string) ($data['esquemafPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo '<br>';
echo _("Ya no hace falta, pero interesa saberlo para acceder al a BD directamente.");
