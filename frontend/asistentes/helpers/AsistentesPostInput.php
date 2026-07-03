<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\helpers\PayloadCoercion;

/**
 * Lectura de ids desde POST (`sel[]` con formato id#… o campos sueltos).
 */
final class AsistentesPostInput
{
    public static function idFromSelPost(string $fallbackField = 'id_activ_old'): int
    {
        $parts = self::selPartsLimited();
        if ($parts !== null) {
            return self::intFromNumericString($parts[0]);
        }
        $idRaw = filter_input(INPUT_POST, $fallbackField, FILTER_VALIDATE_INT);

        return is_int($idRaw) ? $idRaw : 0;
    }

    /**
     * @return array{id_nom: int, id_tabla: string}
     */
    public static function personaFromSelPost(): array
    {
        $parts = self::selPartsLimited();
        if ($parts !== null) {
            return [
                'id_nom' => self::intFromNumericString($parts[0]),
                'id_tabla' => $parts[1],
            ];
        }
        $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

        return [
            'id_nom' => is_int($idNomRaw) ? $idNomRaw : 0,
            'id_tabla' => PayloadCoercion::string(filter_input(INPUT_POST, 'id_tabla')),
        ];
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private static function selPartsLimited(): ?array
    {
        $aSelRaw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!is_array($aSelRaw) || $aSelRaw === []) {
            return null;
        }
        $sel0 = $aSelRaw[0];
        if (!is_string($sel0) || $sel0 === '') {
            return null;
        }
        $parts = explode('#', $sel0, 2);

        return [
            $parts[0],
            $parts[1] ?? '',
        ];
    }

    private static function intFromNumericString(string $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }
}
