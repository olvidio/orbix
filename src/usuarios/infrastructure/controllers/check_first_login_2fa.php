<?php

use core\ConfigGlobal;
use src\usuarios\application\repositories\UsuarioRepository;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// Obtener el usuario actual
$oMiUsuario = ConfigGlobal::MiUsuario();
$id_usuario = $oMiUsuario->getId_usuario();

// Verificar si el usuario tiene 2FA habilitado
$UsuarioRepository = new UsuarioRepository();
$oUsuario = $UsuarioRepository->findById($id_usuario);
$has_2fa = $oUsuario->has2fa();

// Si el usuario no tiene 2FA habilitado, redirigir a la página de configuración de 2FA
if (!$has_2fa) {
    // Generar la URL para la página de configuración de 2FA
    $url_2fa_settings = Hash::cmd(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_form_2fa.php');
    
    // Establecer un mensaje para informar al usuario
    session_start();
    $_SESSION['msg_2fa'] = _("Por razones de seguridad, se recomienda configurar la autenticación de dos factores (2FA).");
    session_write_close();

    // Redirigir a la página de configuración de 2FA
    header("Location: $url_2fa_settings");
    exit();
}

// Si el usuario ya tiene 2FA habilitado, continuar con el flujo normal
// Redirigir a la página de inicio
$url_home = ConfigGlobal::getWeb();
header("Location: $url_home");
exit();