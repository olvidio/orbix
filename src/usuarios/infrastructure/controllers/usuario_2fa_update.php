<?php

use src\usuarios\application\repositories\UsuarioRepository;
use src\usuarios\domain\value_objects\Secret2FA;
use src\usuarios\domain\Verify2fa;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qsecret_2fa = (string)filter_input(INPUT_POST, 'secret_2fa');
$Qenable_2fa = (bool)filter_input(INPUT_POST, 'enable_2fa');
$Qverification_code = (string)filter_input(INPUT_POST, 'verification_code');

$UsuarioRepository = new UsuarioRepository();
$oUsuario = $UsuarioRepository->findById($Qid_usuario);

// Si se está activando 2FA, verificar el código
if ($Qenable_2fa) {
    // Verificar que se haya proporcionado un código de verificación
    if (empty($Qverification_code)) {
        $error_txt = _("Se requiere un código de verificación para activar 2FA");
        ContestarJson::enviar($error_txt, []);
        exit();
    }
    
    // Verificar el código 2FA
    if (!Verify2fa::verify_2fa_code($Qverification_code, $Qsecret_2fa)) {
        $error_txt = _("Código de verificación inválido");
        ContestarJson::enviar($error_txt, []);
        exit();
    }
    
    // Actualizar la configuración 2FA
    $oUsuario->setHas2fa(true);
    $oUsuario->setSecret2fa(new Secret2FA($Qsecret_2fa));
} else {
    // Desactivar 2FA
    $oUsuario->setHas2fa(false);
    // Mantener la clave secreta para una posible reactivación
}

// Guardar los cambios
if ($UsuarioRepository->Guardar($oUsuario) === false) {
    $error_txt .= _("Hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, ['success' => true]);