<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\Secret2FA;
use src\usuarios\domain\Verify2fa;
use src\shared\web\ContestarJson;

$error_txt = '';

$Qid_usuario = (integer)FilterPostGet::post('id_usuario');
$Qsecret_2fa = (string)FilterPostGet::post('secret_2fa');
$Qenable_2fa = (bool)FilterPostGet::post('enable_2fa');
$Qverification_code = (string)FilterPostGet::post('verification_code');

$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oUsuario = $UsuarioRepository->findById($Qid_usuario);
if ($oUsuario === null) {
    ContestarJson::enviar(_('Usuario no encontrado'), []);
    return;
}

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
    $oUsuario->setHas_2fa(true);
    $oUsuario->setSecret2faVo(new Secret2FA($Qsecret_2fa));
} else {
    // Desactivar 2FA
    $oUsuario->setHas_2fa(false);
    // Mantener la clave secreta para una posible reactivación
}

// Guardar los cambios
if ($UsuarioRepository->Guardar($oUsuario) === false) {
    $error_txt .= _("Hay un error, no se ha guardado");
    $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, ['success' => true]);