<?php

declare(strict_types=1);

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EgmItemId;
use Tests\myTest;

final class EgmItemIdTest extends myTest
{
    public function test_create_valid(): void
    {
        $id = new EgmItemId(5);
        $this->assertSame(5, $id->value());
        $this->assertSame('5', (string) $id);
    }

    /**
     * @dataProvider valoresNoPositivosProvider
     */
    public function test_no_positivo_lanza_excepcion(int $valor): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new EgmItemId($valor);
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function valoresNoPositivosProvider(): iterable
    {
        yield 'cero' => [0];
        yield 'negativo' => [-1];
    }

    public function test_minimo_valido_es_uno(): void
    {
        $this->assertSame(1, (new EgmItemId(1))->value());
    }

    public function test_equals(): void
    {
        $this->assertTrue((new EgmItemId(3))->equals(new EgmItemId(3)));
        $this->assertFalse((new EgmItemId(3))->equals(new EgmItemId(4)));
    }

    public function test_from_string_valido(): void
    {
        $this->assertSame(42, EgmItemId::fromString('42')->value());
    }

    /**
     * @dataProvider stringsInvalidosProvider
     */
    public function test_from_string_invalido_lanza_excepcion(string $valor): void
    {
        $this->expectException(\InvalidArgumentException::class);
        EgmItemId::fromString($valor);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function stringsInvalidosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'letras' => ['abc'];
        yield 'negativo' => ['-3'];
        yield 'decimal' => ['1.5'];
        yield 'con espacios' => [' 7'];
    }

    public function test_from_string_cero_lanza_excepcion_de_positivo(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        EgmItemId::fromString('0');
    }

    public function test_from_nullable_int(): void
    {
        $this->assertNull(EgmItemId::fromNullableInt(null));
        $this->assertSame(9, EgmItemId::fromNullableInt(9)?->value());
    }

    public function test_from_nullable_int_cero_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        EgmItemId::fromNullableInt(0);
    }
}
