<?php

declare(strict_types=1);

namespace src\planning\application;

use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Actividades y periodos casa para `planning_casa_ver` (sin ejecutar dominio desde el frontend).
 */
final class PlanningCasaVerData
{
    /**
     * Espera POST alineado con `planning_casa_ver.php` más `f_ini_iso`/`f_fin_iso` (Y-m-d o compatible con {@see DateTimeLocal}).
     *
     * @param array<string, mixed> $post
     * @return array{a_actividades: array, casa_periodos_por_ubi: array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>}
     */
    public static function execute(array $post): array
    {
        $cdc_sel = (int)($post['cdc_sel'] ?? 0);
        $sin_activ = (int)($post['sin_activ'] ?? 0);
        $f_ini_iso = (string)($post['f_ini_iso'] ?? '');
        $f_fin_iso = (string)($post['f_fin_iso'] ?? '');
        if ($f_ini_iso === '' || $f_fin_iso === '') {
            throw new \RuntimeException(_('Faltan fechas de periodo (f_ini_iso / f_fin_iso).'));
        }

        $aIdCdc = null;
        if ($cdc_sel === 9) {
            $sel = (string)($post['sSeleccionados'] ?? '');
            if ($sel !== '') {
                $aIdCdc = array_values(array_filter(array_map('trim', explode(',', $sel)), static fn ($v) => $v !== ''));
            }
        }

        $oIniPlanning = new DateTimeLocal($f_ini_iso);
        $oFinPlanning = new DateTimeLocal($f_fin_iso);
        $oInicioIso = new DateTimeLocal($f_ini_iso);
        $oFinIso = new DateTimeLocal($f_fin_iso);

        [, $a_actividades] = ActividadesPorCasasService::actividadesPorCasas(
            $cdc_sel,
            $oIniPlanning,
            $oFinPlanning,
            $sin_activ,
            $oFinIso,
            $oInicioIso,
            $aIdCdc
        );

        $casaPeriodos = CasaPeriodosForPlanning::collect($a_actividades, $oIniPlanning, $oFinPlanning);

        return [
            'a_actividades' => $a_actividades,
            'casa_periodos_por_ubi' => $casaPeriodos,
        ];
    }
}
