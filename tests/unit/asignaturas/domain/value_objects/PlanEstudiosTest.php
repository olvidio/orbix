<?php

declare(strict_types=1);

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\PlanEstudios;
use Tests\myTest;

final class PlanEstudiosTest extends myTest
{
    public function test_create_valid(): void
    {
        $plan = new PlanEstudios([1997, 2026]);
        $this->assertSame([1997, 2026], $plan->toArray());
    }

    public function test_acepta_strings_numericos_y_quita_duplicados(): void
    {
        $plan = new PlanEstudios(['1997', 1997, ' 2026 ']);
        $this->assertSame([1997, 2026], $plan->toArray());
    }

    public function test_ignora_strings_vacios_o_no_numericos(): void
    {
        $plan = new PlanEstudios(['', '  ', 'abc', '19x7', '-1997', '1997']);
        $this->assertSame([1997], $plan->toArray());
    }

    public function test_lista_vacia_es_valida_en_constructor(): void
    {
        $this->assertSame([], (new PlanEstudios([]))->toArray());
    }

    /**
     * @dataProvider anyosFueraDeRangoProvider
     */
    public function test_anyo_fuera_de_rango_lanza_excepcion(int $anyo): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Plan de estudios inválido');
        new PlanEstudios([$anyo]);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function anyosFueraDeRangoProvider(): iterable
    {
        yield 'justo por debajo de 1928' => [1927];
        yield 'justo por encima de 2200' => [2201];
        yield 'cero' => [0];
        yield 'negativo' => [-1997];
    }

    /**
     * @dataProvider anyosEnRangoNoPermitidosProvider
     */
    public function test_anyo_en_rango_pero_no_permitido_lanza_excepcion(int $anyo): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Plan de estudios no permitido');
        new PlanEstudios([$anyo]);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function anyosEnRangoNoPermitidosProvider(): iterable
    {
        yield 'límite inferior del rango' => [1928];
        yield 'entre planes' => [2000];
        yield 'límite superior del rango' => [2200];
    }

    public function test_un_valor_invalido_entre_validos_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PlanEstudios([1997, 2000]);
    }

    public function test_contains(): void
    {
        $plan = new PlanEstudios([1997]);
        $this->assertTrue($plan->contains(1997));
        $this->assertFalse($plan->contains(2026));
    }

    public function test_to_string_formato_array_pg(): void
    {
        $this->assertSame('{1997,2026}', (string) new PlanEstudios([1997, 2026]));
        $this->assertSame('{}', (string) new PlanEstudios([]));
    }

    public function test_from_array(): void
    {
        $this->assertSame([2026], PlanEstudios::fromArray([2026])->toArray());
    }

    public function test_from_nullable_array(): void
    {
        $this->assertNull(PlanEstudios::fromNullableArray(null));
        $this->assertNull(PlanEstudios::fromNullableArray([]));
        $this->assertSame([1997], PlanEstudios::fromNullableArray([1997])?->toArray());
    }

    public function test_from_pg_string(): void
    {
        $this->assertSame([1997, 2026], PlanEstudios::fromPgString('{1997,2026}')?->toArray());
        $this->assertSame([1997], PlanEstudios::fromPgString('{1997}')?->toArray());
        $this->assertNull(PlanEstudios::fromPgString('{}'));
    }

    public function test_get_array_opciones(): void
    {
        $this->assertSame([1997 => '1997', 2026 => '2026'], PlanEstudios::getArrayOpciones());
    }
}
