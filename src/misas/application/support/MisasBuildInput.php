<?php

declare(strict_types=1);

namespace src\misas\application\support;

final class MisasBuildInput
{
    /**
     * @param array<string, mixed> $in
     */
    public static function int(array $in, string $key, int $default = 0): int
    {
        if (!array_key_exists($key, $in)) {
            return $default;
        }
        $v = $in[$key];
        if (is_int($v)) {
            return $v;
        }
        if (is_string($v) && is_numeric($v)) {
            return (int) $v;
        }

        return $default;
    }

    /**
     * @param array<string, mixed> $in
     */
    public static function string(array $in, string $key, string $default = ''): string
    {
        if (!array_key_exists($key, $in)) {
            return $default;
        }
        $v = $in[$key];
        if (is_string($v)) {
            return $v;
        }
        if (is_int($v) || is_float($v)) {
            return (string) $v;
        }

        return $default;
    }
}
