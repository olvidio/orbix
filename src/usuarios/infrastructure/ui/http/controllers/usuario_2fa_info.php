<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

// id_usuario siempre de la sesión: usuario_2fa_info / usuario_2fa_update solo
// gestionan el 2FA del propio usuario autenticado, nunca el de un tercero.
$Qid_usuario = ConfigGlobal::mi_id_usuario();

$error_txt = '';
$data = [];
if (empty($Qid_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    // Obtener información de 2FA del usuario
    $UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    if ($oUsuario === null) {
        $error_txt = _("Usuario no encontrado");
    } else {
    // Verificar si el usuario tiene 2FA habilitado
    $has_2fa = $oUsuario->isHas_2fa();
    $secret_2fa = $oUsuario->getSecret2faVo()?->value();

    $data['has_2fa'] = $has_2fa;
    $data['secret_2fa'] = $secret_2fa;
    $data['ctx_2fa_update'] = HashB::sign('usuario_2fa_update', ['id_usuario' => $Qid_usuario]);
    }
}

ContestarJson::enviar($error_txt, $data);