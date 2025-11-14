<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;

/**
 * Controlador para restablecer la configuración de autenticación de dos factores (2FA).
 * Este controlador se utiliza cuando un usuario ha perdido acceso a su aplicación de autenticación
 * y necesita desactivar 2FA para poder acceder a su cuenta.
 *
 */
// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// Verificar que el usuario está autenticado
$oMiUsuario = ConfigGlobal::MiUsuario();
$id_usuario = $oMiUsuario->getId_usuario();

// Verificar que el ID de usuario en la solicitud coincide con el usuario autenticado
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

if ($id_usuario !== $Qid_usuario) {
    // Si los IDs no coinciden, mostrar un error y redirigir
    $_SESSION['msg_2fa'] = _("Error: No tiene permiso para realizar esta acción");
    $go_to = ConfigGlobal::getWeb() . "/index.php";
    header("Location: $go_to");
    exit();
}

//////////////////////// Datos del usuario ///////////////////////////////////////////////////
$url_backend = '/src/usuarios/infrastructure/controllers/usuario_guardar.php';
$a_campos_backend = [
    'id_usuario' => $id_usuario,
        'has_2fa' => 'false',
    ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$usuario = $data['usuario'];

if (isset($data['error'])) {
    // Mostrar mensaje de error
    $_SESSION['msg_2fa'] = $data['error'];
} else {
    // Mostrar mensaje de éxito
    $_SESSION['msg_2fa'] = _("Se ha desactivado correctamente la autenticación de dos factores (2FA). Si desea volver a activarla, deberá configurarla nuevamente.");
}

// Redirigir al formulario de 2FA
$url_2fa_form = ConfigGlobal::getWeb() . "/frontend/usuarios/controller/usuario_form_2fa.php";
header("Location: $url_2fa_form");
exit();