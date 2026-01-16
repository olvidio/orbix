<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DelegacionGrupoEstudios;
use Tests\myTest;

class DelegacionGrupoEstudiosTest extends myTest
{
    public function test_create_valid_delegacionGrupoEstudios()
    {
        $delegacionGrupoEstudios = new DelegacionGrupoEstudios('C');
        $this->assertEquals('C', $delegacionGrupoEstudios->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DelegacionGrupoEstudios(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_delegacionGrupoEstudios()
    {
        $delegacionGrupoEstudios1 = new DelegacionGrupoEstudios('C');
        $delegacionGrupoEstudios2 = new DelegacionGrupoEstudios('C');
        $this->assertTrue($delegacionGrupoEstudios1->equals($delegacionGrupoEstudios2));
    }

    public function test_equals_returns_false_for_different_delegacionGrupoEstudios()
    {
        $delegacionGrupoEstudios1 = new DelegacionGrupoEstudios('C');
        $delegacionGrupoEstudios2 = new DelegacionGrupoEstudios('B');
        $this->assertFalse($delegacionGrupoEstudios1->equals($delegacionGrupoEstudios2));
    }

    public function test_to_string_returns_delegacionGrupoEstudios_value()
    {
        $delegacionGrupoEstudios = new DelegacionGrupoEstudios('C');
        $this->assertEquals('C', (string)$delegacionGrupoEstudios);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $delegacionGrupoEstudios = DelegacionGrupoEstudios::fromNullableString('C');
        $this->assertInstanceOf(DelegacionGrupoEstudios::class, $delegacionGrupoEstudios);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $delegacionGrupoEstudios = DelegacionGrupoEstudios::fromNullableString(null);
        $this->assertNull($delegacionGrupoEstudios);
    }

}
