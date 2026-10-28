<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\criteria;

use InvalidArgumentException;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\FilterGroup;
use src\shared\domain\criteria\LogicalOperator;
use Tests\myTest;

class FilterGroupTest extends myTest
{
    public function test_any_of(): void
    {
        $a = Filter::isNull(FakeField::FIni);
        $b = Filter::eq(FakeField::Id, 1);
        $g = FilterGroup::anyOf($a, $b);
        $this->assertSame(LogicalOperator::Or, $g->operator);
        $this->assertSame([$a, $b], $g->children);
    }

    public function test_all_of_anidado(): void
    {
        $inner = FilterGroup::allOf(Filter::gte(FakeField::Id, 1), Filter::lt(FakeField::Id, 9));
        $g = FilterGroup::anyOf(Filter::isNull(FakeField::FIni), $inner);
        $this->assertSame(LogicalOperator::And, $inner->operator);
        $this->assertSame($inner, $g->children[1]);
    }

    public function test_vacio_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        FilterGroup::anyOf();
    }
}
