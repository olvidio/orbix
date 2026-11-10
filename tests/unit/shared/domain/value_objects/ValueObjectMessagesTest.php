<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\ValueObjectMessages;
use Tests\myTest;

final class ValueObjectMessagesTest extends myTest
{
    public function test_safe_repr_string_simple_va_entre_comillas_json(): void
    {
        $this->assertSame('"hola"', ValueObjectMessages::safeRepr('hola'));
    }

    public function test_safe_repr_mantiene_unicode_sin_escapar(): void
    {
        $this->assertSame('"ñandú ’"', ValueObjectMessages::safeRepr('ñandú ’'));
    }

    public function test_safe_repr_escapa_comillas_y_saltos_de_linea(): void
    {
        $this->assertSame('"a\"b\nc"', ValueObjectMessages::safeRepr("a\"b\nc"));
    }

    public function test_safe_repr_vacio(): void
    {
        $this->assertSame('""', ValueObjectMessages::safeRepr(''));
    }

    public function test_safe_repr_utf8_invalido_se_sustituye_sin_fallar(): void
    {
        $out = ValueObjectMessages::safeRepr("ab\xB1cd");
        $this->assertNotSame('(valor no codificable)', $out);
        $this->assertStringContainsString('ab', $out);
        $this->assertStringContainsString('cd', $out);
    }

    public function test_safe_repr_trunca_valores_largos(): void
    {
        $out = ValueObjectMessages::safeRepr(str_repeat('x', 500));
        $this->assertSame(160, strlen($out));
        $this->assertStringEndsWith('...', $out);
    }

    public function test_safe_repr_no_trunca_en_el_limite(): void
    {
        // json = 2 comillas + 158 caracteres = 160 → sin truncar.
        $out = ValueObjectMessages::safeRepr(str_repeat('x', 158));
        $this->assertSame(160, strlen($out));
        $this->assertStringEndsNotWith('...', $out);
    }

    public function test_with_value_context_string(): void
    {
        $this->assertSame(
            'Mal (length=3, value="abc")',
            ValueObjectMessages::withValueContext('Mal', 'abc')
        );
    }

    public function test_with_value_context_cuenta_caracteres_no_bytes(): void
    {
        $this->assertStringContainsString('length=3', ValueObjectMessages::withValueContext('Mal', 'ñañ'));
    }

    public function test_with_value_context_entero(): void
    {
        $this->assertSame('Mal (value="42")', ValueObjectMessages::withValueContext('Mal', 42));
    }
}
