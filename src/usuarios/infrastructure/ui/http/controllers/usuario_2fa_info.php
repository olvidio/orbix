<?php

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\ContestarJson;

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$error_txt = '';
$data = [];
if (empty($Qid_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    // Obtener información de 2FA del usuario
    $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    
    // Verificar si el usuario tiene 2FA habilitado
    $has_2fa = $oUsuario->isHas_2fa();
    $secret_2fa = $oUsuario->getSecret2fa();
    
    $data['has_2fa'] = $has_2fa;
    $data['secret_2fa'] = $secret_2fa;
}

ContestarJson::enviar($error_txt, $data);