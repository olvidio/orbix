<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\DepartamentoId;
use Tests\myTest;

class DepartamentoIdTest extends myTest
{
    public function test_create_valid_departamentoId()
    {
        $departamentoId = new DepartamentoId(123);
        $this->assertEquals(123, $departamentoId->value());
    }

    public function test_equals_returns_true_for_same_departamentoId()
    {
        $departamentoId1 = new DepartamentoId(123);
        $departamentoId2 = new DepartamentoId(123);
        $this->assertTrue($departamentoId1->equals($departamentoId2));
    }

    public function test_equals_returns_false_for_different_departamentoId()
    {
        $departamentoId1 = new DepartamentoId(123);
        $departamentoId2 = new DepartamentoId(456);
        $this->assertFalse($departamentoId1->equals($departamentoId2));
    }

    public function test_to_string_returns_departamentoId_value()
    {
        $departamentoId = new DepartamentoId(123);
        $this->assertEquals(123, (string)$departamentoId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $departamentoId = DepartamentoId::fromNullableInt(123);
        $this->assertInstanceOf(DepartamentoId::class, $departamentoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $departamentoId = DepartamentoId::fromNullableInt(null);
        $this->assertNull($departamentoId);
    }

}
