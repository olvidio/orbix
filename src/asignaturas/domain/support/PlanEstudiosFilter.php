<?php

namespace src\asignaturas\domain\support;

use src\asignaturas\domain\criteria\AsignaturaField;
use src\shared\domain\criteria\Filter;

/**
 * Fragmentos de consulta para filtrar asignaturas por plan de estudios.
 */
final class PlanEstudiosFilter
{
    /**
     * Filtro `Criteria` «la asignatura pertenece al plan» (columna `plan_estudios` integer[]).
     */
    public static function filter(int $plan): Filter
    {
        return Filter::arrayContains(AsignaturaField::PlanEstudios, $plan);
    }

    /**
     * @deprecated usar {@see filter()} con `matching(Criteria)`; solo lo siguen usando consultas específicas
     *             con arrays (`getAsignaturasAsJson`).
     * @param array<string, mixed> $where
     * @param array<string, string> $operators
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    public static function apply(int $plan, array $where = [], array $operators = []): array
    {
        $where['plan_estudios'] = $plan;
        $operators['plan_estudios'] = 'IN_ARRAY';

        return [$where, $operators];
    }
}
