<?php

namespace Tests\unit\asignaturas\domain\support;

use src\asignaturas\domain\criteria\AsignaturaField;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\shared\domain\criteria\Operator;
use Tests\myTest;

class PlanEstudiosFilterTest extends myTest
{
    public function test_filter_es_pertenencia_al_plan_sobre_la_columna_array(): void
    {
        $filtro = PlanEstudiosFilter::filter(2);

        $this->assertSame(AsignaturaField::PlanEstudios, $filtro->field);
        $this->assertSame(Operator::ArrayContains, $filtro->operator);
        $this->assertSame(2, $filtro->value);
    }

    public function test_apply_legacy_sigue_devolviendo_el_par_de_arrays(): void
    {
        [$where, $operators] = PlanEstudiosFilter::apply(1, ['active' => 't'], ['active' => '=']);

        $this->assertSame(['active' => 't', 'plan_estudios' => 1], $where);
        $this->assertSame(['active' => '=', 'plan_estudios' => 'IN_ARRAY'], $operators);
    }
}
