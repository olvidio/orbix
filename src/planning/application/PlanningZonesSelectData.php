<?php

declare(strict_types=1);

namespace src\planning\application;

/**
 * Datos para `planning_zones_select` (`ActividadesPorZonasService`) serializados a JSON
 * (`oIni`/`oFin` como ISO; sin objetos dominio).
 */
final class PlanningZonesSelectData
{
    /**
     * @param array<string, mixed> $post
     * @return array{
     *   actividades_por_zona: array<int, array>,
     *   cabeceras_por_zona: array<int, string>,
     *   zonas: int,
     *   titulo: string,
     *   planning_ini_iso: string,
     *   planning_fin_iso: string
     * }
     */
    public static function execute(array $post): array
    {
        $data = ActividadesPorZonasService::execute(
            (string)($post['id_zona'] ?? ''),
            (int)($post['trimestre'] ?? 0),
            (int)($post['year'] ?? 0),
            (string)($post['actividad'] ?? ''),
            (string)($post['propuesta'] ?? ''),
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
