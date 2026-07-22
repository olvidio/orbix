<?php

declare(strict_types=1);

namespace frontend\actividadessacd\helpers;

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\session\SessionConfig;

final class ActividadessacdSession
{
    public static function oConfig(): bool
    {
        return SessionConfig::isPresent();
    }

    public static function anyFinalCurs(): int
    {
        $any = SessionConfig::anyFinalCurs();
        if ($any > 0) {
            return $any;
        }

        return (int) date('Y');
    }

    public static function sessionIdioma(): string
    {
        $auth = $_SESSION['session_auth'] ?? null;
        if (is_array($auth) && isset($auth['idioma']) && is_string($auth['idioma'])) {
            return $auth['idioma'];
        }

        return PayloadCoercion::string(SessionConfig::getIdiomaDefault());
    }
}
