<?php

namespace Tests\unit\asistentes\domain\value_objects;

use src\asistentes\domain\value_objects\AsistentePropietario;
use Tests\myTest;

class AsistentePropietarioTest extends myTest
{
    public function test_create_valid_asistentePropietario()
    {
        $asistentePropietario = new AsistentePropietario('test value');
        $this->assertEquals('test value', $asistentePropietario->value());
    }

    public function test_to_string_returns_asistentePropietario_value()
    {
        $asistentePropietario = new AsistentePropietario('test value');
        $this->assertEquals('test value', (string)$asistentePropietario);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asistentePropietario = AsistentePropietario::fromNullableString('  test value ');
        $this->assertInstanceOf(AsistentePropietario::class, $asistentePropietario);
        $this->assertSame('test value', $asistentePropietario->value(), 'se recortan los espacios');
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asistentePropietario = AsistentePropietario::fromNullableString(null);
        $this->assertNull($asistentePropietario);
    }

    public function test_fromNullableString_returns_null_for_empty_or_blank_value(): void
    {
        $this->assertNull(AsistentePropietario::fromNullableString(''));
        $this->assertNull(AsistentePropietario::fromNullableString('   '));
    }

    public function test_constructor_trims_the_value(): void
    {
        $this->assertSame('x', (new AsistentePropietario('  x  '))->value());
    }
}
