<?php

declare(strict_types=1);

namespace src\planning\application;

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
            \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_zona'),
            \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'trimestre'),
            \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'year'),
            \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'actividad'),
            \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'propuesta'),
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
