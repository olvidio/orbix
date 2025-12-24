<?php

use src\usuarios\domain\Verify2fa;
use web\ContestarJson;

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
