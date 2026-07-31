<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

/**
 * Coerción segura de valores mixed a tipos escalares (payload JSON, segment data, POST).
 */
final class PayloadCoercion
{
    public static function int(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    public static function string(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        return $default;
    }

    /**
     * @param array<int|string, mixed> $raw
     * @return array<string, mixed>
     */
    public static function stringKeyedArray(array $raw): array
    {
        $out = [];
        foreach ($raw as $key => $value) {
            $out[(string) $key] = $value;
        }

        return $out;
    }
}
