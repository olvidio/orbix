<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence\postgresql;

use InvalidArgumentException;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use Tests\myTest;
use Tests\unit\shared\domain\criteria\FakeField;

class LegacyWhereAdapterTest extends myTest
{
    /** @return array{sql: string, params: array<string, mixed>} */
    private function sql(array $where, array $ops = [], array $columns = []): array
    {
        $adapter = new LegacyWhereAdapter(FakeField::class, $columns);
        return (new PgCriteriaConverter(FakeField::class, $columns))->convert($adapter->toCriteria($where, $ops));
    }

    public function test_vacio(): void
    {
        $this->assertSame(['sql' => '', 'params' => []], $this->sql([]));
    }

    public function test_igualdad_por_defecto_y_varios_campos(): void
    {
        $r = $this->sql(['id' => 5, 'nombre' => 'x']);
        $this->assertSame(' WHERE id = :p0 AND nombre = :p1', $r['sql']);
        $this->assertSame(['p0' => 5, 'p1' => 'x'], $r['params']);
    }

    public function test_comparadores(): void
    {
        $this->assertSame(' WHERE id <> :p0', $this->sql(['id' => 1], ['id' => '!='])['sql']);
        $this->assertSame(' WHERE id <> :p0', $this->sql(['id' => 1], ['id' => '<>'])['sql']);
        $this->assertSame(' WHERE id < :p0', $this->sql(['id' => 1], ['id' => '<'])['sql']);
        $this->assertSame(' WHERE id <= :p0', $this->sql(['id' => 1], ['id' => '<='])['sql']);
        $this->assertSame(' WHERE id > :p0', $this->sql(['id' => 1], ['id' => '>'])['sql']);
        $this->assertSame(' WHERE id >= :p0', $this->sql(['id' => 1], ['id' => '>='])['sql']);
    }

    public function test_is_null_ignora_el_valor(): void
    {
        $this->assertSame(['sql' => ' WHERE id IS NULL', 'params' => []], $this->sql(['id' => ''], ['id' => 'IS NULL']));
        $this->assertSame(' WHERE id IS NOT NULL', $this->sql(['id' => 'x'], ['id' => 'IS NOT NULL'])['sql']);
    }

    public function test_regex_y_sin_acentos_y_bits(): void
    {
        $this->assertSame(' WHERE nombre::text ~ :p0', $this->sql(['nombre' => '^a'], ['nombre' => '~'])['sql']);
        $this->assertSame(' WHERE nombre::text ~* :p0', $this->sql(['nombre' => '^a'], ['nombre' => '~*'])['sql']);
        $this->assertSame(' WHERE nombre::text !~ :p0', $this->sql(['nombre' => '^a'], ['nombre' => '!~'])['sql']);
        $this->assertSame(' WHERE nombre::text !~* :p0', $this->sql(['nombre' => '^a'], ['nombre' => '!~*'])['sql']);
        $this->assertStringContainsString('sin_acentos', $this->sql(['nombre' => 'a'], ['nombre' => 'sin_acentos'])['sql']);
        $r = $this->sql(['id' => '6'], ['id' => '&']);
        $this->assertSame(' WHERE (id & :p0) = :p1', $r['sql']);
        $this->assertSame(['p0' => 6, 'p1' => 6], $r['params']);
    }

    public function test_or_con_valores_por_coma_es_in(): void
    {
        $r = $this->sql(['id' => '1,3,7'], ['id' => 'OR']);
        $this->assertSame(' WHERE id IN (:p0, :p1, :p2)', $r['sql']);
        $this->assertSame(['p0' => 1, 'p1' => 3, 'p2' => 7], $r['params']);
    }

    public function test_between_con_cadena_y_con_comillas(): void
    {
        $r = $this->sql(['id' => '1,9'], ['id' => 'BETWEEN']);
        $this->assertSame(' WHERE id BETWEEN :p0 AND :p1', $r['sql']);
        $this->assertSame(['p0' => 1, 'p1' => 9], $r['params']);

        $r = $this->sql(['f_ini' => "'2026-01-01','2026-02-01'"], ['f_ini' => 'BETWEEN']);
        $this->assertSame(['p0' => '2026-01-01', 'p1' => '2026-02-01'], $r['params']);
    }

    public function test_between_con_un_solo_valor_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sql(['id' => '1'], ['id' => 'BETWEEN']);
    }

    public function test_any_con_literal_de_array_pg(): void
    {
        $r = $this->sql(['id' => '{1, 3, 7}'], ['id' => 'ANY']);
        $this->assertSame(' WHERE id IN (:p0, :p1, :p2)', $r['sql']);
        $this->assertSame([1, 3, 7], array_values($r['params']));
    }

    public function test_in_con_lista_mapa_y_cadena(): void
    {
        $this->assertSame([1, 2], array_values($this->sql(['id' => [1, 2]], ['id' => 'IN'])['params']));
        // mapa id => etiqueta: se usan las claves
        $this->assertSame([501, 1003], array_values($this->sql(['id' => [501 => 'A', 1003 => 'Z']], ['id' => 'IN'])['params']));
        // cadena ya formateada, con comillas
        $r = $this->sql(['nombre' => "'a','b,c'"], ['nombre' => 'NOT IN']);
        $this->assertSame(' WHERE nombre NOT IN (:p0, :p1)', $r['sql']);
        $this->assertSame(['a', 'b,c'], array_values($r['params']));
    }

    public function test_in_vacio_no_lanza_excepcion(): void
    {
        $this->assertSame(' WHERE FALSE', $this->sql(['id' => []], ['id' => 'IN'])['sql']);
    }

    public function test_in_array(): void
    {
        $this->assertSame(' WHERE :p0 = ANY(id)', $this->sql(['id' => 4], ['id' => 'IN_ARRAY'])['sql']);
    }

    public function test_valor_null_conserva_resultado_vacio(): void
    {
        $this->assertSame(' WHERE (FALSE)', $this->sql(['id' => null])['sql']);
    }

    public function test_txt_es_sql_crudo(): void
    {
        $r = $this->sql(['nombre' => "COALESCE(nombre,'x') ~* 'C\\d\\d'", 'id' => 1], ['nombre' => 'TXT']);
        $this->assertSame(" WHERE (COALESCE(nombre,'x') ~* 'C\\d\\d') AND id = :p0", $r['sql']);
        $this->assertSame(['p0' => 1], $r['params']);
    }

    public function test_inv(): void
    {
        $r = $this->sql(['nombre' => '123'], ['nombre' => '~INV']);
        $this->assertSame(' WHERE (:p0::text ~ nombre)', $r['sql']);
        $this->assertSame(['p0' => '123'], $r['params']);
    }

    public function test_like(): void
    {
        $r = $this->sql(['nombre' => 'a%'], ['nombre' => 'ilike']);
        $this->assertSame(' WHERE (nombre ILIKE :p0)', $r['sql']);
    }

    public function test_operador_desconocido_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sql(['id' => 1], ['id' => 'FOO']);
    }

    public function test_columna_sin_campo_usa_filtro_de_columna(): void
    {
        $r = $this->sql(['otra_col' => 3], ['otra_col' => '>']);
        $this->assertSame(' WHERE otra_col > :p0', $r['sql']);
        $this->assertSame(['p0' => 3], $r['params']);
    }

    public function test_columna_sin_campo_con_nombre_peligroso_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sql(['id; DROP TABLE x' => 3]);
    }

    public function test_mapa_de_columnas(): void
    {
        $cols = ['nombre' => 'nom_completo'];
        $this->assertSame(' WHERE nom_completo = :p0', $this->sql(['nom_completo' => 'a'], [], $cols)['sql']);
        // la columna 'nombre' ya no es el campo 'nombre': está remapeado
        $this->assertSame(' WHERE nombre = :p0', $this->sql(['nombre' => 'a'], [], $cols)['sql']);
    }

    public function test_ordre_simple_y_compuesto(): void
    {
        $r = $this->sql(['_ordre' => 'nombre, f_ini DESC NULLS LAST']);
        $this->assertSame(' ORDER BY nombre ASC, f_ini DESC NULLS LAST', $r['sql']);
    }

    public function test_ordre_con_expresion_va_crudo(): void
    {
        $r = $this->sql(['_ordre' => 'substring(nombre::text,1), id']);
        $this->assertSame(' ORDER BY substring(nombre::text,1), id ASC', $r['sql']);
        $r = $this->sql(['_ordre' => '(json_x::json->0)::text DESC']);
        $this->assertSame(' ORDER BY (json_x::json->0)::text DESC', $r['sql']);
    }

    public function test_ordre_con_mapa_de_columnas(): void
    {
        $r = $this->sql(['_ordre' => 'nom_completo DESC'], [], ['nombre' => 'nom_completo']);
        $this->assertSame(' ORDER BY nom_completo DESC', $r['sql']);
    }

    public function test_limit(): void
    {
        $r = $this->sql(['_limit' => 20]);
        $this->assertSame(' LIMIT :p0', $r['sql']);
        $this->assertSame(['p0' => 20], $r['params']);
        $r = $this->sql(['_limit' => '10 OFFSET 5']);
        $this->assertSame(' LIMIT :p0 OFFSET :p1', $r['sql']);
        $this->assertSame(['p0' => 10, 'p1' => 5], $r['params']);
    }

    public function test_limit_no_valido_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sql(['_limit' => '1; DROP TABLE x']);
    }

    public function test_clase_que_no_es_campo_lanza_excepcion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new LegacyWhereAdapter(\stdClass::class);
    }
}
