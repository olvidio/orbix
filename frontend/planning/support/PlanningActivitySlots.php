<?php

declare(strict_types=1);

namespace frontend\planning\support;

/**
 * Convierte fechas/horas de actividad en indices de columna del planning
 * (cada dia se divide en 3 franjas cuando idd=3).
 */
final class PlanningActivitySlots
{
    /**
     * @return array{n_dini: int, n_dfi: int}
     */
    public static function indices(
        int $idd,
        int $numSecIni0,
        int $diaIni,
        int $mesIni,
        int $anyIni,
        int $hIni,
        int $mIni,
        int $sIni,
        int $diaFi,
        int $mesFi,
        int $anyFi,
        int $hFi,
        int $mFi,
        int $sFi,
    ): array {
        $tsDayIni = mktime(0, 0, 0, $mesIni, $diaIni, $anyIni);
        $tsDayFi = mktime(0, 0, 0, $mesFi, $diaFi, $anyFi);
        $diasIni = $tsDayIni !== false ? (int) floor(($tsDayIni - $numSecIni0) / 86400) : 0;
        $diasFi = $tsDayFi !== false ? (int) floor(($tsDayFi - $numSecIni0) / 86400) : 0;

        if ($idd <= 1) {
            return [
                'n_dini' => 1 + $idd * $diasIni,
                'n_dfi' => 1 + $idd * $diasFi,
            ];
        }

        $horaIni = $hIni + $mIni / 60 + $sIni / 3600;
        $horaFi = $hFi + $mFi / 60 + $sFi / 3600;

        return [
            'n_dini' => self::franjaInicio($horaIni) + $idd * $diasIni,
            'n_dfi' => self::franjaFin($horaFi) + $idd * $diasFi,
        ];
    }

    /**
     * Franja de inicio: mañana (<10), día (<20), tarde (>=20).
     */
    public static function franjaInicio(float $hora): int
    {
        if ($hora < 10) {
            return 1;
        }
        if ($hora < 20) {
            return 2;
        }

        return 3;
    }

    /**
     * Franja de fin: mañana (<=10), día (<=20), tarde (>20).
     */
    public static function franjaFin(float $hora): int
    {
        if ($hora <= 10) {
            return 1;
        }
        if ($hora <= 20) {
            return 2;
        }

        return 3;
    }
}
