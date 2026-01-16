<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\CongresoTipo;
use Tests\myTest;

class CongresoTipoTest extends myTest
{
    public function test_create_valid_congresoTipo()
    {
        $congresoTipo = new CongresoTipo(CongresoTipo::CV);
        $this->assertEquals(1, $congresoTipo->value());
    }

    public function test_invalid_congresoTipo_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CongresoTipo(999);
    }

    public function test_equals_returns_true_for_same_congresoTipo()
    {
        $congresoTipo1 = new CongresoTipo(CongresoTipo::CV);
        $congresoTipo2 = new CongresoTipo(CongresoTipo::CV);
        $this->assertTrue($congresoTipo1->equals($congresoTipo2));
    }

    public function test_equals_returns_false_for_different_congresoTipo()
    {
        $congresoTipo1 = new CongresoTipo(CongresoTipo::CV);
        $congresoTipo2 = new CongresoTipo(CongresoTipo::CONGRESO);
        $this->assertFalse($congresoTipo1->equals($congresoTipo2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $congresoTipo = CongresoTipo::fromNullableInt(CongresoTipo::CV);
        $this->assertInstanceOf(CongresoTipo::class, $congresoTipo);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $congresoTipo = CongresoTipo::fromNullableInt(null);
        $this->assertNull($congresoTipo);
    }

}
