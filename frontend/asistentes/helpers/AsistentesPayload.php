<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

/**
 * Normalización de payloads planos del segmento asistentes.
 */
final class AsistentesPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function postData(mixed $data): array
    {
        if (!is_array($data)) {
            return [];
        }
        $out = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}
