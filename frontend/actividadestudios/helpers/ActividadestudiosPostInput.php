<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\helpers\PayloadCoercion;

/**
 * Lectura de ids desde POST (`sel[]` con formato id#… o campos sueltos).
 */
final class ActividadestudiosPostInput
{
    public static function idFromSel(): int
    {
        $parts = self::selPartsLimited();
        if ($parts !== null) {
            return self::intFromNumericString($parts[0]);
        }

        $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
        $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);
        if (is_int($idActivRaw)) {
            return $idActivRaw;
        }

        return is_int($idNomRaw) ? $idNomRaw : 0;
    }

    /**
     * @return array{id_activ: int, id_asignatura: int}
     */
    public static function idActivAsignatura(): array
    {
        $token = self::selToken();
        if ($token !== null) {
            $parts = explode('#', $token);

            return [
                'id_activ' => self::intFromNumericString($parts[0]),
                'id_asignatura' => self::intFromNumericString($parts[1] ?? ''),
            ];
        }

        $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);
        $idAsigRaw = filter_input(INPUT_POST, 'id_asignatura', FILTER_VALIDATE_INT);

        return [
            'id_activ' => is_int($idActivRaw) ? $idActivRaw : 0,
            'id_asignatura' => is_int($idAsigRaw) ? $idAsigRaw : 0,
        ];
    }

    /**
     * @return array{id_nom: int}
     */
    public static function idNom(): array
    {
        $parts = self::selPartsLimited();
        if ($parts !== null) {
            return [
                'id_nom' => self::intFromNumericString($parts[0]),
            ];
        }

        $idNomRaw = filter_input(INPUT_POST, 'id_nom', FILTER_VALIDATE_INT);

        return [
            'id_nom' => is_int($idNomRaw) ? $idNomRaw : 0,
        ];
    }

    /**
     * @return array{id_activ: int, nom_activ: string}
     */
    public static function idActivNom(): array
    {
        $parts = self::selPartsLimited();
        if ($parts !== null) {
            return [
                'id_activ' => self::intFromNumericString($parts[0]),
                'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string($parts[1]),
            ];
        }

        $idActivRaw = filter_input(INPUT_POST, 'id_activ', FILTER_VALIDATE_INT);

        return [
            'id_activ' => is_int($idActivRaw) ? $idActivRaw : 0,
            'nom_activ' => \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'nom_activ')),
        ];
    }

    private static function selToken(): ?string
    {
        if (array_key_exists('sel', $_POST)) {
            $raw = $_POST['sel'];
            if (is_array($raw)) {
                $sel0 = $raw[0] ?? null;
                if (is_string($sel0) && $sel0 !== '') {
                    return $sel0;
                }
            } elseif (is_scalar($raw) && (string) $raw !== '') {
                return (string) $raw;
            }
        }

        $aSelRaw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!is_array($aSelRaw) || $aSelRaw === []) {
            return null;
        }
        $sel0 = $aSelRaw[0];
        if (!is_string($sel0) || $sel0 === '') {
            return null;
        }

        return $sel0;
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private static function selPartsLimited(): ?array
    {
        $token = self::selToken();
        if ($token === null) {
            return null;
        }
        $parts = explode('#', $token, 2);

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
