<?php

declare(strict_types=1);

namespace frontend\dossiers\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class DossiersPayload
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function listRows(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @return array<string, int>
     */
    public static function permBitMap(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $key => $value) {
            if (is_string($key)) {
                $out[$key] = \frontend\shared\helpers\PayloadCoercion::int($value);
            }
        }

        return $out;
    }

    /**
     * @param array<int|string, mixed> $data
     * @return array<string, mixed>
     */
    public static function viewVariables(array $data): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}
