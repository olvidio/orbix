<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\domain\value_objects;

use src\pasarela\domain\value_objects\PasarelaParametroCode;
use Tests\myTest;

final class PasarelaParametroCodeTest extends myTest
{
    public function test_create_valid(): void
    {
        $code = new PasarelaParametroCode('curso_crt');
        $this->assertSame('curso_crt', $code->value());
        $this->assertSame('curso_crt', (string) $code);
    }

    public function test_hace_trim(): void
    {
        $this->assertSame('idioma-default', (new PasarelaParametroCode("  idioma-default \n"))->value());
    }

    public function test_longitud_maxima_64_valida(): void
    {
        $this->assertSame(64, strlen((new PasarelaParametroCode(str_repeat('a', 64)))->value()));
    }

    public function test_longitud_65_lanza_excepcion_con_contexto(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be at most 64 characters');
        new PasarelaParametroCode(str_repeat('a', 65));
    }

    /**
     * @dataProvider vaciosProvider
     */
    public function test_vacio_lanza_excepcion(string $valor): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('cannot be empty');
        new PasarelaParametroCode($valor);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function vaciosProvider(): iterable
    {
        yield 'cadena vacía' => [''];
        yield 'solo espacios' => ['   '];
    }

    /**
     * @dataProvider invalidosProvider
     */
    public function test_caracteres_invalidos_lanzan_excepcion(string $valor): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid characters');
        new PasarelaParametroCode($valor);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidosProvider(): iterable
    {
        yield 'espacio interior' => ['a b'];
        yield 'punto' => ['a.b'];
        yield 'acento' => ['curso_ñ'];
        yield 'barra' => ['a/b'];
    }

    public function test_equals(): void
    {
        $this->assertTrue((new PasarelaParametroCode('a'))->equals(new PasarelaParametroCode(' a ')));
        $this->assertFalse((new PasarelaParametroCode('a'))->equals(new PasarelaParametroCode('b')));
    }

    public function test_from_string(): void
    {
        $this->assertSame('x_1', PasarelaParametroCode::fromString('x_1')->value());
    }

    public function test_from_nullable_string(): void
    {
        $this->assertNull(PasarelaParametroCode::fromNullableString(null));
        $this->assertNull(PasarelaParametroCode::fromNullableString(''));
        $this->assertNull(PasarelaParametroCode::fromNullableString('   '));
        $this->assertSame('ok', PasarelaParametroCode::fromNullableString(' ok ')?->value());
    }

    public function test_from_nullable_string_invalido_lanza_excepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PasarelaParametroCode::fromNullableString('a b');
    }
}
