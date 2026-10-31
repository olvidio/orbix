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
}
