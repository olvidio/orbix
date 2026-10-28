<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\criteria;

use InvalidArgumentException;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\OrderDirection;
use Tests\myTest;

class CriteriaTest extends myTest
{
    public function test_create_vacio(): void
    {
        $c = Criteria::create();
        $this->assertSame([], $c->filters());
        $this->assertSame([], $c->orders());
        $this->assertNull($c->getLimit());
        $this->assertNull($c->getOffset());
    }

    public function test_where_y_and_where(): void
    {
        $a = Filter::eq(FakeField::Id, 1);
        $b = Filter::eq(FakeField::Nombre, 'x');
        $c = Criteria::create()->where($a)->andWhere($b);
        $this->assertSame([$a, $b], $c->filters());
    }

    public function test_where_sustituye(): void
    {
        $a = Filter::eq(FakeField::Id, 1);
        $b = Filter::eq(FakeField::Nombre, 'x');
        $c = Criteria::create()->where($a)->where($b);
        $this->assertSame([$b], $c->filters());
    }

    public function test_es_inmutable(): void
    {
        $base = Criteria::create()->where(Filter::eq(FakeField::Id, 1));
        $especializado = $base->andWhere(Filter::eq(FakeField::Nombre, 'x'))->limit(5);
        $this->assertCount(1, $base->filters());
        $this->assertNull($base->getLimit());
        $this->assertCount(2, $especializado->filters());
        $this->assertSame(5, $especializado->getLimit());
    }

    public function test_order_by_sustituye_y_add_acumula(): void
    {
        $c = Criteria::create()
            ->orderBy(FakeField::Id)
            ->orderBy(FakeField::Nombre, OrderDirection::Desc)
            ->addOrderBy(FakeField::FIni, OrderDirection::Asc, NullsPosition::Last);
        $orders = $c->orders();
        $this->assertCount(2, $orders);
        $this->assertSame(FakeField::Nombre, $orders[0]->field);
        $this->assertSame(OrderDirection::Desc, $orders[0]->direction);
        $this->assertNull($orders[0]->nulls);
        $this->assertSame(FakeField::FIni, $orders[1]->field);
        $this->assertSame(NullsPosition::Last, $orders[1]->nulls);
    }

    public function test_order_por_defecto_asc(): void
    {
        $o = Criteria::create()->orderBy(FakeField::Id)->orders()[0];
        $this->assertSame(OrderDirection::Asc, $o->direction);
    }

    public function test_limit_y_offset(): void
    {
        $c = Criteria::create()->limit(20)->offset(40);
        $this->assertSame(20, $c->getLimit());
        $this->assertSame(40, $c->getOffset());
        $this->assertNull($c->limit(null)->getLimit());
    }

    public function test_limit_negativo_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Criteria::create()->limit(-1);
    }

    public function test_offset_negativo_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Criteria::create()->offset(-1);
    }

    public function test_conservar_orden_y_limite_al_filtrar(): void
    {
        $c = Criteria::create()->orderBy(FakeField::Id)->limit(3)->offset(1)->where(Filter::eq(FakeField::Id, 1));
        $this->assertCount(1, $c->orders());
        $this->assertSame(3, $c->getLimit());
        $this->assertSame(1, $c->getOffset());
    }
}
