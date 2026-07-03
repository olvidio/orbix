<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Login JSON para app móvil (Camino B). Establece sesión PHP y cookies como login web.
 *
 * Cuerpo JSON (o form POST): username, password, esquema (opcional si ESQUEMA en env),
 * verification_code (opcional; obligatorio si el usuario tiene 2FA configurado).
 *
 * Respuesta: mismo envelope que ContestarJson; data incluye códigos de app (need_2fa, etc.).
 */

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\AppMobileLogin;
use src\shared\web\ContestarJson;

header('Content-Type: application/json; charset=UTF-8');

$raw = file_get_contents('php://input');
/** @var array<string, mixed> $json */
$json = [];
if (is_string($raw) && $raw !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $json = $decoded;
    }
}

$merged = array_merge($_POST, $json);

/** @var array{username?: string, password?: string, esquema?: string, verification_code?: string} $input */
$input = [
    'username' => FuncTablasSupport::inputString($merged, 'username'),
    'password' => FuncTablasSupport::inputString($merged, 'password'),
    'esquema' => FuncTablasSupport::inputString($merged, 'esquema'),
    'verification_code' => FuncTablasSupport::inputString($merged, 'verification_code'),
];

/** @var AppMobileLogin $useCase */
$useCase = DependencyResolver::get(AppMobileLogin::class);
$result = $useCase->execute($input);

if ($result['ok']) {
    ContestarJson::enviar('', $result['data'] ?? []);
    return;
}

$code = (string)($result['code'] ?? 'error');
$mensaje = (string)($result['mensaje'] ?? _('Error de autenticación'));
$data = array_merge(['code' => $code], $result['data'] ?? []);

ContestarJson::enviar($mensaje, $data);
