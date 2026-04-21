<?php

namespace src\misas\application;

/**
 * Color en BD: exactamente 6 caracteres hexadecimales en minúsculas, sin '#'.
 */
final class InicialesColorHex
{
    public static function normalizeForStorage(string $color): string
    {
        $color = trim($color);
        if ($color === '') {
            return '';
        }
        if (isset($color[0]) && $color[0] === '#') {
            $color = substr($color, 1);
        }
        if (strlen($color) === 3 && preg_match('/^[0-9A-Fa-f]{3}$/', $color) === 1) {
            $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
        }
        if (strlen($color) === 6 && preg_match('/^[0-9A-Fa-f]{6}$/', $color) === 1) {
            return strtolower($color);
        }

        return '';
    }
}
