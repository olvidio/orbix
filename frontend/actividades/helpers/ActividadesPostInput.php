<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class ActividadesPostInput
{
public static function idActivFromPost(): int
{
    $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (is_array($a_sel_raw) && $a_sel_raw !== []) {
        $sel0 = $a_sel_raw[0];
        if (is_string($sel0) && $sel0 !== '') {
            $parts = explode('#', $sel0, 2);
            $idRaw = $parts[0];

            return is_numeric($idRaw) ? (int) $idRaw : 0;
        }
    }

    $idRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);

    return is_int($idRaw) ? $idRaw : 0;
}

public static function posicionString(mixed $value, string $default = ''): string
{
    return is_string($value) || is_int($value) || is_float($value) ? PayloadCoercion::string($value) : $default;
}

public static function posicionInt(mixed $value, int $default = 0): int
{
    return is_int($value) || is_string($value) ? PayloadCoercion::int($value, $default) : $default;
}
}
