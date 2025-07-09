<?php

use src\usuarios\domain\Verify2fa;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// Obtener los parámetros
$verification_code = filter_input(INPUT_POST, 'verification_code');
$secret_2fa = filter_input(INPUT_POST, 'secret_2fa');

$error_txt = '';
$data = [];

if (empty($verification_code) || empty($secret_2fa)) {
    $error_txt = _("Código de verificación o clave secreta no válidos");
} else {
    // Verificar el código 2FA
    if (Verify2fa::verify_2fa_code($verification_code, $secret_2fa)) {
        $data['valid'] = true;
    } else {
        $error_txt = _("Código de verificación inválido");
        $data['valid'] = false;
    }
}

ContestarJson::enviar($error_txt, $data);
