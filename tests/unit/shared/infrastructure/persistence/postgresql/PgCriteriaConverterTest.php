<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence\postgresql;

use DateTimeImmutable;
use InvalidArgumentException;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\CriteriaField;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\FilterGroup;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\OrderDirection;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use Tests\myTest;
use Tests\unit\shared\domain\criteria\FakeField;

enum OtroField: string implements CriteriaField
{
    case Id = 'id';
}

class PgCriteriaConverterTest extends myTest
{
    private PgCriteriaConverter $conv;

    public function setUp(): void
    {
        parent::setUp();
        $this->conv = new PgCriteriaConverter(FakeField::class);
    }

    private function where(Filter|FilterGroup $f): array
    {
        return $this->conv->convertWhere(Criteria::create()->where($f));
    }

    public function test_criteria_vacio(): void
    {
        $r = $this->conv->convert(Criteria::create());
        $this->assertSame(['sql' => '', 'params' => []], $r);
    }

    public function test_comparaciones(): void
    {
        $casos = [
            [Filter::eq(FakeField::Id, 5), ' WHERE id = :p0'],
            [Filter::neq(FakeField::Id, 5), ' WHERE id <> :p0'],
            [Filter::lt(FakeField::Id, 5), ' WHERE id < :p0'],
            [Filter::lte(FakeField::Id, 5), ' WHERE id <= :p0'],
            [Filter::gt(FakeField::Id, 5), ' WHERE id > :p0'],
            [Filter::gte(FakeField::Id, 5), ' WHERE id >= :p0'],
        ];
        foreach ($casos as [$filtro, $sql]) {
            $r = $this->where($filtro);
            $this->assertSame($sql, $r['sql']);
            $this->assertSame(['p0' => 5], $r['params']);
        }
    }

    public function test_is_null(): void
    {
        $this->assertSame(['sql' => ' WHERE f_ini IS NULL', 'params' => []], $this->where(Filter::isNull(FakeField::FIni)));
        $this->assertSame(['sql' => ' WHERE f_ini IS NOT NULL', 'params' => []], $this->where(Filter::isNotNull(FakeField::FIni)));
    }

    public function test_in_y_not_in_enlazan_cada_valor(): void
    {
        $r = $this->where(Filter::in(FakeField::Id, [1, 3, 7]));
        $this->assertSame(' WHERE id IN (:p0, :p1, :p2)', $r['sql']);
        $this->assertSame(['p0' => 1, 'p1' => 3, 'p2' => 7], $r['params']);

        $r = $this->where(Filter::notIn(FakeField::Nombre, ['a']));
        $this->assertSame(' WHERE nombre NOT IN (:p0)', $r['sql']);
        $this->assertSame(['p0' => 'a'], $r['params']);
    }

    public function test_in_vacio(): void
    {
        $this->assertSame(['sql' => ' WHERE FALSE', 'params' => []], $this->where(Filter::in(FakeField::Id, [])));
        $this->assertSame(['sql' => ' WHERE TRUE', 'params' => []], $this->where(Filter::notIn(FakeField::Id, [])));
    }

    public function test_between(): void
    {
        $r = $this->where(Filter::between(FakeField::Id, 1, 9));
        $this->assertSame(' WHERE id BETWEEN :p0 AND :p1', $r['sql']);
        $this->assertSame(['p0' => 1, 'p1' => 9], $r['params']);
    }

    public function test_array_contains(): void
    {
        $r = $this->where(Filter::arrayContains(FakeField::Id, 4));
        $this->assertSame(' WHERE :p0 = ANY(id)', $r['sql']);
        $this->assertSame(['p0' => 4], $r['params']);
    }

    public function test_regex(): void
    {
        $this->assertSame(' WHERE nombre::text ~ :p0', $this->where(Filter::matches(FakeField::Nombre, '^a'))['sql']);
        $this->assertSame(' WHERE nombre::text ~* :p0', $this->where(Filter::matchesInsensitive(FakeField::Nombre, '^a'))['sql']);
        $this->assertSame(' WHERE nombre::text !~ :p0', $this->where(Filter::notMatches(FakeField::Nombre, '^a'))['sql']);
        $this->assertSame(' WHERE nombre::text !~* :p0', $this->where(Filter::notMatchesInsensitive(FakeField::Nombre, '^a'))['sql']);
    }

    public function test_sin_acentos(): void
    {
        $r = $this->where(Filter::matchesUnaccented(FakeField::Nombre, 'jose'));
        $this->assertSame(' WHERE public.sin_acentos(nombre::text) ~* public.sin_acentos(:p0::text)', $r['sql']);
        $this->assertSame(['p0' => 'jose'], $r['params']);
    }

    public function test_has_bits_usa_dos_parametros(): void
    {
        $r = $this->where(Filter::hasBits(FakeField::Id, 6));
        $this->assertSame(' WHERE (id & :p0) = :p1', $r['sql']);
        $this->assertSame(['p0' => 6, 'p1' => 6], $r['params']);
    }

    public function test_filtros_de_primer_nivel_se_unen_con_and(): void
    {
        $c = Criteria::create()
            ->where(Filter::eq(FakeField::Id, 1))
            ->andWhere(Filter::eq(FakeField::Nombre, 'x'));
        $r = $this->conv->convertWhere($c);
        $this->assertSame(' WHERE id = :p0 AND nombre = :p1', $r['sql']);
        $this->assertSame(['p0' => 1, 'p1' => 'x'], $r['params']);
    }

    public function test_grupos_anidados_con_parentesis(): void
    {
        $g = FilterGroup::anyOf(
            Filter::isNull(FakeField::FIni),
            FilterGroup::allOf(Filter::gte(FakeField::FIni, 10), Filter::eq(FakeField::Id, 2)),
        );
        $r = $this->where($g);
        $this->assertSame(' WHERE (f_ini IS NULL OR (f_ini >= :p0 AND id = :p1))', $r['sql']);
        $this->assertSame(['p0' => 10, 'p1' => 2], $r['params']);
    }

    public function test_el_mismo_campo_puede_aparecer_varias_veces(): void
    {
        $c = Criteria::create()
            ->where(Filter::gt(FakeField::Id, 1))
            ->andWhere(Filter::lt(FakeField::Id, 9));
        $r = $this->conv->convertWhere($c);
        $this->assertSame(' WHERE id > :p0 AND id < :p1', $r['sql']);
    }

    public function test_orden_limite_y_offset(): void
    {
        $c = Criteria::create()
            ->where(Filter::eq(FakeField::Id, 1))
            ->orderBy(FakeField::Nombre)
            ->addOrderBy(FakeField::FIni, OrderDirection::Desc, NullsPosition::Last)
            ->limit(20)
            ->offset(40);
        $r = $this->conv->convert($c);
        $this->assertSame(
            ' WHERE id = :p0 ORDER BY nombre ASC, f_ini DESC NULLS LAST LIMIT :p1 OFFSET :p2',
            $r['sql']
        );
        $this->assertSame(['p0' => 1, 'p1' => 20, 'p2' => 40], $r['params']);
    }

    public function test_convert_where_ignora_orden_y_limite(): void
    {
        $c = Criteria::create()->where(Filter::eq(FakeField::Id, 1))->orderBy(FakeField::Id)->limit(5);
        $this->assertSame(['sql' => ' WHERE id = :p0', 'params' => ['p0' => 1]], $this->conv->convertWhere($c));
    }

    public function test_solo_orden(): void
    {
        $r = $this->conv->convert(Criteria::create()->orderBy(FakeField::Id));
        $this->assertSame(' ORDER BY id ASC', $r['sql']);
    }

    public function test_los_parametros_se_reinician_en_cada_llamada(): void
    {
        $c = Criteria::create()->where(Filter::eq(FakeField::Id, 1));
        $this->conv->convert($c);
        $r = $this->conv->convert($c);
        $this->assertSame(['p0' => 1], $r['params']);
    }

    public function test_mapa_campo_columna(): void
    {
        $conv = new PgCriteriaConverter(FakeField::class, ['nombre' => 'nom_completo', 'id' => 't.id_x']);
        $c = Criteria::create()->where(Filter::eq(FakeField::Nombre, 'a'))->orderBy(FakeField::Id);
        $r = $conv->convert($c);
        $this->assertSame(' WHERE nom_completo = :p0 ORDER BY t.id_x ASC', $r['sql']);
    }

    public function test_campo_de_otro_enum_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->where(Filter::eq(OtroField::Id, 1));
    }

    public function test_orden_con_campo_de_otro_enum_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->conv->convert(Criteria::create()->orderBy(OtroField::Id));
    }

    public function test_columna_invalida_en_el_mapa_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PgCriteriaConverter(FakeField::class, ['id' => 'id; DROP TABLE x']);
    }

    public function test_clase_que_no_es_campo_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PgCriteriaConverter(\stdClass::class);
    }

    public function test_normalizacion_de_valores(): void
    {
        $vo = new class {
            public function value(): int
            {
                return 7;
            }
        };
        $fecha = new DateTimeImmutable('2026-03-04 05:06:07', new \DateTimeZone('UTC'));
        $c = Criteria::create()
            ->where(Filter::eq(FakeField::Id, true))
            ->andWhere(Filter::eq(FakeField::Nombre, $vo))
            ->andWhere(Filter::eq(FakeField::FIni, $fecha))
            ->andWhere(Filter::eq(FakeField::Id, OrderDirection::Asc));
        $p = $this->conv->convertWhere($c)['params'];
        $this->assertSame('t', $p['p0']);
        $this->assertSame(7, $p['p1']);
        $this->assertSame('2026-03-04 05:06:07.000000+00:00', $p['p2']);
        $this->assertSame('asc', $p['p3']);
    }

    public function test_valores_peligrosos_no_aparecen_en_el_sql(): void
    {
        $r = $this->where(Filter::eq(FakeField::Nombre, "x'; DROP TABLE y; --"));
        $this->assertSame(' WHERE nombre = :p0', $r['sql']);
        $this->assertSame(["x'; DROP TABLE y; --"], array_values($r['params']));
    }
}
