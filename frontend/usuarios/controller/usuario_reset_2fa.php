<?php

use core\ConfigGlobal;
use src\usuarios\application\repositories\UsuarioRepository;

/**
 * Controlador para restablecer la configuración de autenticación de dos factores (2FA).
 * Este controlador se utiliza cuando un usuario ha perdido acceso a su aplicación de autenticación
 * y necesita desactivar 2FA para poder acceder a su cuenta.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

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

// Obtener el objeto de usuario
$UsuarioRepository = new UsuarioRepository();
$oUsuario = $UsuarioRepository->findById($id_usuario);

// Desactivar 2FA para el usuario
$oUsuario->setHas2fa(false);
// Mantener la clave secreta para una posible reactivación

// Guardar los cambios
if ($UsuarioRepository->Guardar($oUsuario) === false) {
    $error_txt = _("Hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
    
    // Mostrar mensaje de error
    $_SESSION['msg_2fa'] = $error_txt;
} else {
    // Mostrar mensaje de éxito
    $_SESSION['msg_2fa'] = _("Se ha desactivado correctamente la autenticación de dos factores (2FA). Si desea volver a activarla, deberá configurarla nuevamente.");
}

// Redirigir al formulario de 2FA
$url_2fa_form = ConfigGlobal::getWeb() . "/frontend/usuarios/controller/usuario_form_2fa.php";
header("Location: $url_2fa_form");
exit();