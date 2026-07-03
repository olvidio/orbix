<?php

declare(strict_types=1);

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

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
echo PayloadCoercion::string($data['esquema'] ?? '') . ' > ' . htmlspecialchars(PayloadCoercion::string($data['esquemaPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo PayloadCoercion::string($data['esquemav'] ?? '') . ' > ' . htmlspecialchars(PayloadCoercion::string($data['esquemavPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo PayloadCoercion::string($data['esquemaf'] ?? '') . ' > ' . htmlspecialchars(PayloadCoercion::string($data['esquemafPwd'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
echo '<br>';
echo _("Ya no hace falta, pero interesa saberlo para acceder al a BD directamente.");
