<?php

namespace src\personas\domain\value_objects;

/**
 * Validación y mensajes de error para textos de persona (nombre, apellidos, etc.).
 */
final class PersonaTextoChars
{
    /** Nombre, apellidos y apellido familiar. */
    public const CLASE_TEXTO_PERSONA = "[\p{L}\p{M}0-9 .,'’´\-()?·\/\*]";

    public const CLASE_NX = '[A-Za-z0-9 ]';

    public const CLASE_TABLA_CODE = '[A-Za-z0-9_]';

    public const CLASE_SITUACION = "[\p{L}0-9 .,'’_\-()\+]";

    public const CLASE_TRATO = '[\p{L}\p{M}\p{N}\p{P}\p{S}\p{Z}]';

    public static function normalizeTipografico(string $value): string
    {
        $value = str_replace(
            [
                "\u{2013}", "\u{2014}", "\u{2212}",
                "\u{2018}", "\u{2019}", "\u{201B}",
                '`',
            ],
            ['-', '-', '-', "'", "'", "'", "'"],
            $value
        );

        if (function_exists('normalizer_normalize')) {
            $normalized = normalizer_normalize($value, \Normalizer::FORM_C);
            if (is_string($normalized)) {
                $value = $normalized;
            }
        }

        return $value;
    }

    public static function fullPattern(string $charClass): string
    {
        return '/^' . $charClass . '+$/u';
    }

    public static function throwsIfNotMatching(string $valueObjectName, string $value, string $charClass): void
    {
        if (preg_match(self::fullPattern($charClass), $value)) {
            return;
        }
        throw new \InvalidArgumentException(self::invalidCharactersMessage($valueObjectName, $value, $charClass));
    }

    public static function invalidCharactersMessage(string $valueObjectName, string $value, string $charClass): string
    {
        return sprintf(
            '%s has invalid characters (value=%s; no permitidos: %s)',
            $valueObjectName,
            self::safeRepr($value),
            self::disallowedCharsSummary($value, $charClass)
        );
    }

    public static function safeRepr(string $value): string
    {
        $flags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_SUBSTITUTE')) {
            $flags |= JSON_INVALID_SUBSTITUTE;
        }
        $json = json_encode($value, $flags);
        if (!is_string($json)) {
            return '(valor no codificable)';
        }
        if (strlen($json) > 160) {
            return substr($json, 0, 157) . '...';
        }

        return $json;
    }

    /**
     * @return non-empty-string
     */
    public static function disallowedCharsSummary(string $value, string $charClass): string
    {
        $charPattern = '/^' . $charClass . '$/u';
        $seen = [];
        $parts = [];
        $len = mb_strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($value, $i, 1);
            if (preg_match($charPattern, $ch)) {
                continue;
            }
            $ord = mb_ord($ch, 'UTF-8');
            $key = $ch . "\0" . (string)$ord;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $enc = json_encode($ch, JSON_UNESCAPED_UNICODE);
            $parts[] = sprintf('%s (U+%04X)', is_string($enc) ? $enc : '?', $ord >= 0 ? $ord : 0);
        }

        return $parts !== [] ? implode(', ', $parts) : '(sin detalle)';
    }
}
