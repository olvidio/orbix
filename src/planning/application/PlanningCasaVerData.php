<?php

declare(strict_types=1);

namespace src\planning\application;

use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Actividades y periodos casa para `planning_casa_ver`.
 */
final class PlanningCasaVerData
{
    public function __construct(
        private ActividadesPorCasasService $actividadesPorCasasService,
        private CasaPeriodosForPlanning $casaPeriodosForPlanning,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   a_actividades: array<int|string, array<string, list<array<string, mixed>>>>,
     *   casa_periodos_por_ubi: array<int, array<int, array{iso_ini: string, iso_fin: string, sfsv: int}>>
     * }
     */
    public function execute(array $input): array
    {
        $cdc_sel = FuncTablasSupport::inputInt($input, 'cdc_sel');
        $sin_activ = FuncTablasSupport::inputInt($input, 'sin_activ');
        $f_ini_iso = FuncTablasSupport::inputString($input, 'f_ini_iso');
        $f_fin_iso = FuncTablasSupport::inputString($input, 'f_fin_iso');
        if ($f_ini_iso === '' || $f_fin_iso === '') {
            throw new \RuntimeException(_('Faltan fechas de periodo (f_ini_iso / f_fin_iso).'));
        }

        $aIdCdc = null;
        if ($cdc_sel === 9) {
            $sel = FuncTablasSupport::inputString($input, 'sSeleccionados');
            if ($sel !== '') {
                $aIdCdc = array_values(array_filter(array_map('trim', explode(',', $sel)), static fn ($v) => $v !== ''));
            }
        }

        $oIniPlanning = new DateTimeLocal($f_ini_iso);
        $oFinPlanning = new DateTimeLocal($f_fin_iso);
        $oInicioIso = new DateTimeLocal($f_ini_iso);
        $oFinIso = new DateTimeLocal($f_fin_iso);

        [, $a_actividades] = $this->actividadesPorCasasService->actividadesPorCasas(
            $cdc_sel,
            $oIniPlanning,
            $oFinPlanning,
            $sin_activ,
            $oFinIso,
            $oInicioIso,
            $aIdCdc
        );

        $casaPeriodos = $this->casaPeriodosForPlanning->collect($a_actividades, $oIniPlanning, $oFinPlanning);

        return [
            'a_actividades' => $a_actividades,
            'casa_periodos_por_ubi' => $casaPeriodos,
        ];
    }
}
