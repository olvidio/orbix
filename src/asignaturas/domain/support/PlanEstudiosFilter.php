<?php

namespace src\asignaturas\domain\support;

/**
 * Fragmentos de consulta para filtrar asignaturas por plan de estudios.
 */
final class PlanEstudiosFilter
{
    /**
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
