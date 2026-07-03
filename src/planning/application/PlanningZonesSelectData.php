<?php

declare(strict_types=1);

namespace src\planning\application;

use src\shared\domain\helpers\FuncTablasSupport;
/**
 * Datos para `planning_zones_select` serializados a JSON.
 */
final class PlanningZonesSelectData
{
    public function __construct(
        private ActividadesPorZonasService $actividadesPorZonasService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   actividades_por_zona: array<int, array<int|string, mixed>>,
     *   cabeceras_por_zona: array<int, string>,
     *   zonas: int,
     *   titulo: string,
     *   planning_ini_iso: string,
     *   planning_fin_iso: string
     * }
     */
    public function execute(array $input): array
    {
        $data = $this->actividadesPorZonasService->execute(
            FuncTablasSupport::inputString($input, 'id_zona'),
            FuncTablasSupport::inputInt($input, 'trimestre'),
            FuncTablasSupport::inputInt($input, 'year'),
            FuncTablasSupport::inputString($input, 'actividad'),
            FuncTablasSupport::inputString($input, 'propuesta'),
            null
        );

        $ini = $data['oIniPlanning'];
        $fin = $data['oFinPlanning'];
        unset($data['oIniPlanning'], $data['oFinPlanning']);

        $data['planning_ini_iso'] = $ini->format('Y-m-d');
        $data['planning_fin_iso'] = $fin->format('Y-m-d');

        return $data;
    }
}
