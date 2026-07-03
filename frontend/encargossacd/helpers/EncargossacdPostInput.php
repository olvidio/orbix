<?php

declare(strict_types=1);

namespace frontend\encargossacd\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class EncargossacdPostInput
{
    public static function postString(string $name, string $default = ''): string
    {
        return PayloadCoercion::string(filter_input(INPUT_POST, $name), $default);
    }

    public static function postInt(string $name, int $default = 0): int
    {
        $raw = filter_input(INPUT_POST, $name, FILTER_VALIDATE_INT);
    
        return is_int($raw) ? $raw : $default;
    }

    public static function postSelIdItemH(int $fallback = 0): int
    {
        $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!is_array($a_sel_raw) || $a_sel_raw === []) {
            return $fallback;
        }
        $sel0 = $a_sel_raw[0];
        if (!is_string($sel0) || $sel0 === '') {
            return $fallback;
        }
        $parts = explode('#', $sel0, 2);
    
        return is_numeric($parts[0]) ? (int) $parts[0] : $fallback;
    }

    public static function selIdFromPost(string $fallbackField = 'id_enc'): int
    {
        $a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (is_array($a_sel_raw) && $a_sel_raw !== []) {
            $sel0 = $a_sel_raw[0];
            if (is_string($sel0) && $sel0 !== '') {
                $parts = explode('#', $sel0, 2);
    
                return is_numeric($parts[0]) ? (int) $parts[0] : 0;
            }
        }
    
        return self::postInt($fallbackField);
    }

}
