<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$id_usuario = usuarios_session_auth_int('id_usuario');
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

if ($id_usuario !== $Qid_usuario) {
    $_SESSION['msg_2fa'] = _("Error: No tiene permiso para realizar esta acción");
    $go_to = AppUrlConfig::getPublicAppBaseUrl() . "/index.php";
    header("Location: $go_to");
    exit();
}

PostRequest::getDataFromUrl('/src/usuarios/usuario_2fa_update', [
    'id_usuario' => $id_usuario,
    'enable_2fa' => '0',
]);

$_SESSION['msg_2fa'] = _("Se ha desactivado correctamente la autenticación de dos factores (2FA). Si desea volver a activarla, deberá configurarla nuevamente.");

$url_2fa_form = AppUrlConfig::getPublicAppBaseUrl() . "/frontend/usuarios/controller/usuario_form_2fa.php";
header("Location: $url_2fa_form");
exit();
