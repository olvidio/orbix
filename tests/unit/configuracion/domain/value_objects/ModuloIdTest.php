<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\ModuloId;
use Tests\myTest;

class ModuloIdTest extends myTest
{
    public function test_create_valid_moduloId()
    {
        $moduloId = new ModuloId(123);
        $this->assertEquals(123, $moduloId->value());
    }

    public function test_equals_returns_true_for_same_moduloId()
    {
        $moduloId1 = new ModuloId(123);
        $moduloId2 = new ModuloId(123);
        $this->assertTrue($moduloId1->equals($moduloId2));
    }

    public function test_equals_returns_false_for_different_moduloId()
    {
        $moduloId1 = new ModuloId(123);
        $moduloId2 = new ModuloId(456);
        $this->assertFalse($moduloId1->equals($moduloId2));
    }

    public function test_to_string_returns_moduloId_value()
    {
        $moduloId = new ModuloId(123);
        $this->assertEquals(123, (string)$moduloId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $moduloId = ModuloId::fromNullableInt(123);
        $this->assertInstanceOf(ModuloId::class, $moduloId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $moduloId = ModuloId::fromNullableInt(null);
        $this->assertNull($moduloId);
    }

}
