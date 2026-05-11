<?php
/**
 * JSON: resultado de {@see \src\permisos\domain\PermisosActividades::getPermisoCrear}
 * para crear una actividad nueva (tipo + dl_propia). Ejecutado bajo bootstrap con DI;
 * desde solo-frontend llamar con PostRequest y cookies de sesión.
 */

use src\shared\web\ContestarJson;

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
if ($Qid_tipo_activ === '') {
    $Qid_tipo_activ = (string)filter_input(INPUT_GET, 'id_tipo_activ');
}

$Qdl = (string)filter_input(INPUT_POST, 'dl_propia');
if ($Qdl === '') {
    $Qdl = (string)filter_input(INPUT_GET, 'dl_propia');
}
$dl_propia = !($Qdl === 'f' || $Qdl === '0' || strcasecmp($Qdl, 'false') === 0);

if (!isset($_SESSION['oPermActividades'])) {
    ContestarJson::enviar(_('Sesión sin permisos de actividades'), []);

    return;
}

$_SESSION['oPermActividades']->setId_tipo_activ($Qid_tipo_activ);

ob_start();
$result = $_SESSION['oPermActividades']->getPermisoCrear($dl_propia);
$aviso = ob_get_clean();

ContestarJson::enviar('', [
    'permiso_crear' => $result,
    'aviso' => trim((string)$aviso),
]);
