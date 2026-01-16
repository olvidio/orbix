<?php

namespace Tests\unit\casas\domain\value_objects;

use src\casas\domain\value_objects\IngresoNumAsistentes;
use Tests\myTest;

class IngresoNumAsistentesTest extends myTest
{
    public function test_create_valid_ingresoNumAsistentes()
    {
        $ingresoNumAsistentes = new IngresoNumAsistentes(123);
        $this->assertEquals(123, $ingresoNumAsistentes->value());
    }

    public function test_equals_returns_true_for_same_ingresoNumAsistentes()
    {
        $ingresoNumAsistentes1 = new IngresoNumAsistentes(123);
        $ingresoNumAsistentes2 = new IngresoNumAsistentes(123);
        $this->assertTrue($ingresoNumAsistentes1->equals($ingresoNumAsistentes2));
    }

    public function test_equals_returns_false_for_different_ingresoNumAsistentes()
    {
        $ingresoNumAsistentes1 = new IngresoNumAsistentes(123);
        $ingresoNumAsistentes2 = new IngresoNumAsistentes(456);
        $this->assertFalse($ingresoNumAsistentes1->equals($ingresoNumAsistentes2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $ingresoNumAsistentes = IngresoNumAsistentes::fromNullableInt(123);
        $this->assertInstanceOf(IngresoNumAsistentes::class, $ingresoNumAsistentes);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $ingresoNumAsistentes = IngresoNumAsistentes::fromNullableInt(null);
        $this->assertNull($ingresoNumAsistentes);
    }

}
