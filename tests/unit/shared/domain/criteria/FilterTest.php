<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\criteria;

use DateTimeImmutable;
use InvalidArgumentException;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Operator;
use Tests\myTest;

class FilterTest extends myTest
{
    public function test_eq(): void
    {
        $f = Filter::eq(FakeField::Id, 5);
        $this->assertSame(FakeField::Id, $f->field);
        $this->assertSame(Operator::Eq, $f->operator);
        $this->assertSame(5, $f->value);
    }

    public function test_operadores_simples(): void
    {
        $this->assertSame(Operator::Neq, Filter::neq(FakeField::Id, 1)->operator);
        $this->assertSame(Operator::Lt, Filter::lt(FakeField::Id, 1)->operator);
        $this->assertSame(Operator::Lte, Filter::lte(FakeField::Id, 1)->operator);
        $this->assertSame(Operator::Gt, Filter::gt(FakeField::Id, 1)->operator);
        $this->assertSame(Operator::Gte, Filter::gte(FakeField::Id, 1)->operator);
        $this->assertSame(Operator::ArrayContains, Filter::arrayContains(FakeField::Id, 1)->operator);
        $this->assertSame(Operator::HasBits, Filter::hasBits(FakeField::Id, 4)->operator);
    }

    public function test_regex(): void
    {
        $this->assertSame(Operator::Matches, Filter::matches(FakeField::Nombre, '^a')->operator);
        $this->assertSame(Operator::MatchesInsensitive, Filter::matchesInsensitive(FakeField::Nombre, '^a')->operator);
        $this->assertSame(Operator::NotMatches, Filter::notMatches(FakeField::Nombre, '^a')->operator);
        $this->assertSame(Operator::NotMatchesInsensitive, Filter::notMatchesInsensitive(FakeField::Nombre, '^a')->operator);
        $this->assertSame(Operator::MatchesUnaccented, Filter::matchesUnaccented(FakeField::Nombre, '^a')->operator);
    }

    public function test_is_null_no_lleva_valor(): void
    {
        $this->assertNull(Filter::isNull(FakeField::FIni)->value);
        $this->assertSame(Operator::IsNotNull, Filter::isNotNull(FakeField::FIni)->operator);
    }

    public function test_in_reindexa_la_lista(): void
    {
        $f = Filter::in(FakeField::Id, [5 => 1, 9 => 3]);
        $this->assertSame([1, 3], $f->value);
    }

    public function test_in_vacio_es_valido(): void
    {
        $this->assertSame([], Filter::in(FakeField::Id, [])->value);
    }

    public function test_between(): void
    {
        $d1 = new DateTimeImmutable('2026-01-01');
        $d2 = new DateTimeImmutable('2026-12-31');
        $f = Filter::between(FakeField::FIni, $d1, $d2);
        $this->assertSame([$d1, $d2], $f->value);
    }

    public function test_eq_null_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Filter::eq(FakeField::Id, null);
    }

    public function test_in_con_null_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Filter::in(FakeField::Id, [1, null]);
    }

    public function test_valor_array_en_eq_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Filter::eq(FakeField::Id, [1, 2]);
    }

    public function test_objeto_sin_value_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Filter::eq(FakeField::Id, new \stdClass());
    }

    public function test_value_object_con_value_es_valido(): void
    {
        $vo = new class {
            public function value(): int
            {
                return 7;
            }
        };
        $this->assertSame($vo, Filter::eq(FakeField::Id, $vo)->value);
    }

    public function test_operator_helpers(): void
    {
        $this->assertFalse(Operator::IsNull->takesValue());
        $this->assertTrue(Operator::Eq->takesValue());
        $this->assertTrue(Operator::Between->takesList());
        $this->assertFalse(Operator::Eq->takesList());
    }
}
