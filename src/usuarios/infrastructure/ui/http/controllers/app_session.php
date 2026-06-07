<?php

/**
 * Comprueba si hay sesión autenticada (útil al arrancar la app). Sin credenciales.
 */

use src\shared\web\ContestarJson;

header('Content-Type: application/json; charset=UTF-8');

$sessionAuth = $_SESSION['session_auth'] ?? null;
if (is_array($sessionAuth) && !empty($sessionAuth['id_usuario'])) {
    ContestarJson::enviar('', [
        'authenticated' => true,
        'id_usuario' => is_int($sessionAuth['id_usuario'])
            ? $sessionAuth['id_usuario']
            : (is_numeric($sessionAuth['id_usuario']) ? (int)$sessionAuth['id_usuario'] : 0),
        'username' => is_string($sessionAuth['username'] ?? null) ? $sessionAuth['username'] : '',
        'esquema' => is_string($sessionAuth['esquema'] ?? null) ? $sessionAuth['esquema'] : '',
    ]);
    return;
}

ContestarJson::enviar('', [
    'authenticated' => false,
]);
