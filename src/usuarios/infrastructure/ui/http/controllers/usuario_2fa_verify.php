<?php

use src\usuarios\domain\Verify2fa;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

// Obtener los parámetros
$verification_code = FilterPostGet::post('verification_code');
$secret_2fa = FilterPostGet::post('secret_2fa');

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
