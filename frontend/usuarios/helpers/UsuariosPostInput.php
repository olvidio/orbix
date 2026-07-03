<?php

declare(strict_types=1);

namespace frontend\usuarios\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class UsuariosPostInput
{
public static function selFirstItem(mixed $a_sel): mixed
{
    if (!is_array($a_sel)) {
        return null;
    }
    foreach ($a_sel as $item) {
        return $item;
    }

    return null;
}

public static function idFromSelItem(mixed $sel0): int
{
    if (!is_string($sel0) || $sel0 === '') {
        return 0;
    }
    $parts = explode('#', $sel0, 2);
    $idRaw = $parts[0];

    return is_numeric($idRaw) ? (int) $idRaw : 0;
}

public static function idFromSelSecond(mixed $sel0): int
{
    if (!is_string($sel0) || $sel0 === '') {
        return 0;
    }
    $parts = explode('#', $sel0, 2);
    if (!isset($parts[1])) {
        return 0;
    }

    return is_numeric($parts[1]) ? (int) $parts[1] : 0;
}

public static function sessionAuthInt(string $key): int
{
    $sessionAuth = $_SESSION['session_auth'] ?? null;
    if (!is_array($sessionAuth)) {
        return 0;
    }

    return \frontend\shared\helpers\PayloadCoercion::int($sessionAuth[$key] ?? 0);
}

public static function sessionAuthString(string $key, string $default = ''): string
{
    $sessionAuth = $_SESSION['session_auth'] ?? null;
    if (!is_array($sessionAuth)) {
        return $default;
    }

    return \frontend\shared\helpers\PayloadCoercion::string($sessionAuth[$key] ?? $default);
}

public static function requestString(string $key): string
{
    $merged = array_merge(UsuariosPayload::postData($_GET), UsuariosPayload::postData($_POST));

    return \frontend\shared\helpers\PayloadCoercion::string($merged[$key] ?? '');
}

public static function loginInputFromPost(): array
{
    $post = UsuariosPayload::postData($_POST);

    return [
        'username' => \frontend\shared\helpers\PayloadCoercion::string($post['username'] ?? ''),
        'password' => \frontend\shared\helpers\PayloadCoercion::string($post['password'] ?? ''),
        'esquema' => \frontend\shared\helpers\PayloadCoercion::string($post['esquema'] ?? ''),
        'verification_code' => \frontend\shared\helpers\PayloadCoercion::string($post['verification_code'] ?? ''),
    ];
}

public static function recoverySessionIdFromCookie(): ?string
{
    $cookie = $_COOKIE['PHPSESSID'] ?? null;
    if (!is_string($cookie) || $cookie === '') {
        return null;
    }

    return $cookie;
}
}
