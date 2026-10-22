<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoCentroCode;
use Tests\myTest;

class TipoCentroCodeTest extends myTest
{
    public function test_create_valid_tipoCentroCode()
    {
        $tipoCentroCode = new TipoCentroCode('test');
        $this->assertEquals('test', $tipoCentroCode->value());
    }

    public function test_acepta_guion_barra_vertical_y_coma()
    {
        $this->assertSame('a-b', (new TipoCentroCode('a-b'))->value());
        $this->assertSame('cr,ce', (new TipoCentroCode('cr,ce'))->value());
        $this->assertSame('a|b', (new TipoCentroCode('a|b'))->value());
        $this->assertSame('a_b', (new TipoCentroCode('a_b'))->value());
    }

    /**
     * @dataProvider caracteresNoPermitidosProvider
     */
    public function test_caracter_no_permitido_throws_exception(string $valor)
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCentroCode($valor);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function caracteresNoPermitidosProvider(): array
    {
        // Algunos de estos caracteres (] ^ ` { ) los dejaba pasar el rango «_-|» de la regex anterior.
        return [
            'asterisco' => ['*****'],
            'corchete' => ['a]b'],
            'circunflejo' => ['a^b'],
            'acento grave' => ['a`b'],
            'llave' => ['a{b'],
            'espacio' => ['a b'],
        ];
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCentroCode(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tipoCentroCode()
    {
        $tipoCentroCode1 = new TipoCentroCode('test');
        $tipoCentroCode2 = new TipoCentroCode('test');
        $this->assertTrue($tipoCentroCode1->equals($tipoCentroCode2));
    }

    public function test_equals_returns_false_for_different_tipoCentroCode()
    {
        $tipoCentroCode1 = new TipoCentroCode('test');
        $tipoCentroCode2 = new TipoCentroCode('alte');
        $this->assertFalse($tipoCentroCode1->equals($tipoCentroCode2));
    }

    public function test_to_string_returns_tipoCentroCode_value()
    {
        $tipoCentroCode = new TipoCentroCode('test');
        $this->assertEquals('test', (string)$tipoCentroCode);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tipoCentroCode = TipoCentroCode::fromNullableString('test');
        $this->assertInstanceOf(TipoCentroCode::class, $tipoCentroCode);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tipoCentroCode = TipoCentroCode::fromNullableString(null);
        $this->assertNull($tipoCentroCode);
    }

}
