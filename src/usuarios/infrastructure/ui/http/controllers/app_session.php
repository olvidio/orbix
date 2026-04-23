<?php

/**
 * Comprueba si hay sesión autenticada (útil al arrancar la app). Sin credenciales.
 */

use web\ContestarJson;

header('Content-Type: application/json; charset=UTF-8');

if (!empty($_SESSION['session_auth']['id_usuario'])) {
    ContestarJson::enviar('', [
        'authenticated' => true,
        'id_usuario' => (int)$_SESSION['session_auth']['id_usuario'],
        'username' => (string)($_SESSION['session_auth']['username'] ?? ''),
        'esquema' => (string)($_SESSION['session_auth']['esquema'] ?? ''),
    ]);
    return;
}

ContestarJson::enviar('', [
    'authenticated' => false,
]);
