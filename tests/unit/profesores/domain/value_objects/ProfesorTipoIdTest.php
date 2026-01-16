<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\ProfesorTipoId;
use Tests\myTest;

class ProfesorTipoIdTest extends myTest
{
    public function test_create_valid_profesorTipoId()
    {
        $profesorTipoId = new ProfesorTipoId(123);
        $this->assertEquals(123, $profesorTipoId->value());
    }

    public function test_equals_returns_true_for_same_profesorTipoId()
    {
        $profesorTipoId1 = new ProfesorTipoId(123);
        $profesorTipoId2 = new ProfesorTipoId(123);
        $this->assertTrue($profesorTipoId1->equals($profesorTipoId2));
    }

    public function test_equals_returns_false_for_different_profesorTipoId()
    {
        $profesorTipoId1 = new ProfesorTipoId(123);
        $profesorTipoId2 = new ProfesorTipoId(456);
        $this->assertFalse($profesorTipoId1->equals($profesorTipoId2));
    }

    public function test_to_string_returns_profesorTipoId_value()
    {
        $profesorTipoId = new ProfesorTipoId(123);
        $this->assertEquals(123, (string)$profesorTipoId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $profesorTipoId = ProfesorTipoId::fromNullableInt(123);
        $this->assertInstanceOf(ProfesorTipoId::class, $profesorTipoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $profesorTipoId = ProfesorTipoId::fromNullableInt(null);
        $this->assertNull($profesorTipoId);
    }

}
