<?php

use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();

$id_usuario = UsuariosPostInput::sessionAuthInt('id_usuario');
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

if ($id_usuario !== $Qid_usuario) {
    $_SESSION['msg_2fa'] = _("Error: No tiene permiso para realizar esta acción");
    $go_to = AppUrlConfig::getPublicAppBaseUrl() . "/index.php";
    header("Location: $go_to");
    exit();
}

$Qctx_2fa_update = (string)filter_input(INPUT_POST, 'ctx_2fa_update');

PostRequest::getDataFromUrl('/src/usuarios/usuario_2fa_update', [
    'ctx_2fa_update' => $Qctx_2fa_update,
    'enable_2fa' => '0',
]);

$_SESSION['msg_2fa'] = _("Se ha desactivado correctamente la autenticación de dos factores (2FA). Si desea volver a activarla, deberá configurarla nuevamente.");

$url_2fa_form = AppUrlConfig::getPublicAppBaseUrl() . "/frontend/usuarios/controller/usuario_form_2fa.php";
header("Location: $url_2fa_form");
exit();
