<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\session\SessionConfig;

final class ActividadestudiosConfig
{
    public static function oConfig(): bool
    {
        return SessionConfig::isPresent();
    }

    public static function notaMaxDefault(): int
    {
        return PayloadCoercion::int(SessionConfig::getNotaMax('0'));
    }
}
