<?php

use src\shared\domain\helpers\FilterPostGet;

/**
 * JSON: resultado de {@see \src\permisos\domain\PermisosActividades::getPermisoCrear}
 * para crear una actividad nueva (tipo + dl_propia). Ejecutado bajo bootstrap con DI;
 * desde solo-frontend llamar con PostRequest y cookies de sesión.
 */

use src\permisos\domain\PermisosActividades;
use src\shared\web\ContestarJson;

$Qid_tipo_activ = (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ');
if ($Qid_tipo_activ === '') {
    $Qid_tipo_activ = (string)\src\shared\domain\helpers\FilterPostGet::get('id_tipo_activ');
}

$Qdl = (string)\src\shared\domain\helpers\FilterPostGet::post('dl_propia');
if ($Qdl === '') {
    $Qdl = (string)\src\shared\domain\helpers\FilterPostGet::get('dl_propia');
}
$dl_propia = !($Qdl === 'f' || $Qdl === '0' || strcasecmp($Qdl, 'false') === 0);

$oPermActividades = $_SESSION['oPermActividades'] ?? null;
if (!($oPermActividades instanceof PermisosActividades)) {
    ContestarJson::enviar(_('Sesión sin permisos de actividades'), []);

    return;
}

$oPermActividades->setId_tipo_activ($Qid_tipo_activ);

ob_start();
$result = $oPermActividades->getPermisoCrear($dl_propia);
$aviso = ob_get_clean();

ContestarJson::enviar('', [
    'permiso_crear' => $result,
    'aviso' => trim((string)$aviso),
]);
