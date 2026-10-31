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
}
