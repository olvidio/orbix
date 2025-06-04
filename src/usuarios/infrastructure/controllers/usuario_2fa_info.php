<?php

use src\usuarios\application\repositories\UsuarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

$error_txt = '';
$data = [];
if (empty($Qid_usuario)) {
    $error_txt = _("Id de usuario no válido");
} else {
    // Obtener información de 2FA del usuario
    $UsuarioRepository = new UsuarioRepository();
    $oUsuario = $UsuarioRepository->findById($Qid_usuario);
    
    // Verificar si el usuario tiene 2FA habilitado
    $has_2fa = $oUsuario->has2fa();
    $secret_2fa = $oUsuario->getSecret2fa();
    
    $data['has_2fa'] = $has_2fa;
    $data['secret_2fa'] = $secret_2fa;
}

ContestarJson::enviar($error_txt, $data);