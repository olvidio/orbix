<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\helpers\PayloadCoercion;
use src\configuracion\domain\value_objects\ConfigSnapshot;

final class ActividadestudiosConfig
{
    public static function oConfig(): ?ConfigSnapshot
    {
        $oConfig = $_SESSION['oConfig'] ?? null;

        return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
    }

    public static function notaMaxDefault(): int
    {
        $oConfig = self::oConfig();

        return $oConfig !== null ? \frontend\shared\helpers\PayloadCoercion::int($oConfig->getNotaMax()) : 0;
    }
}
