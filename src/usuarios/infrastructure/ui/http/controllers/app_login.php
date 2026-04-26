<?php

/**
 * Login JSON para app móvil (Camino B). Establece sesión PHP y cookies como login web.
 *
 * Cuerpo JSON (o form POST): username, password, esquema (opcional si ESQUEMA en env),
 * verification_code (opcional; obligatorio si el usuario tiene 2FA configurado).
 *
 * Respuesta: mismo envelope que ContestarJson; data incluye códigos de app (need_2fa, etc.).
 */

use src\usuarios\application\AppMobileLogin;
use frontend\shared\web\ContestarJson;

header('Content-Type: application/json; charset=UTF-8');

$raw = file_get_contents('php://input');
$json = [];
if (is_string($raw) && $raw !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $json = $decoded;
    }
}

$input = array_merge(
    [
        'username' => (string)filter_input(INPUT_POST, 'username'),
        'password' => (string)filter_input(INPUT_POST, 'password'),
        'esquema' => (string)filter_input(INPUT_POST, 'esquema'),
        'verification_code' => (string)filter_input(INPUT_POST, 'verification_code'),
    ],
    $json
);

$result = AppMobileLogin::attempt($input);

if ($result['ok']) {
    ContestarJson::enviar('', $result['data'] ?? []);
    return;
}

$code = (string)($result['code'] ?? 'error');
$mensaje = (string)($result['mensaje'] ?? _('Error de autenticación'));
$data = array_merge(['code' => $code], $result['data'] ?? []);

ContestarJson::enviar($mensaje, $data);
