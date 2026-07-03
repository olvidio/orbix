<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class ActividadestudiosDesplegableSupport
{
    public static function blanco(mixed $value): bool|string
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1 ? '1' : '';
        }

        return PayloadCoercion::string($value);
    }

    public static function opcionSel(mixed $value): string
    {
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return PayloadCoercion::string($value);
    }
}
