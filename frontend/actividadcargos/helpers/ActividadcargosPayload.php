<?php

declare(strict_types=1);

namespace frontend\actividadcargos\helpers;

final class ActividadcargosPayload
{
    /**
     * @param array<int|string, mixed> $raw
     * @return array<string, mixed>
     */
    public static function stringKeyPayload(array $raw): array
    {
        $out = [];
        foreach ($raw as $key => $value) {
            if (is_string($key)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}
