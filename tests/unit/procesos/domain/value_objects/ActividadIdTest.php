<?php

namespace Tests\unit\procesos\domain\value_objects;

use src\procesos\domain\value_objects\ActividadId;
use Tests\myTest;

class ActividadIdTest extends myTest
{
    public function test_create_valid_actividadId()
    {
        $actividadId = new ActividadId(123);
        $this->assertEquals(123, $actividadId->value());
    }

    public function test_equals_returns_true_for_same_actividadId()
    {
        $actividadId1 = new ActividadId(123);
        $actividadId2 = new ActividadId(123);
        $this->assertTrue($actividadId1->equals($actividadId2));
    }

    public function test_equals_returns_false_for_different_actividadId()
    {
        $actividadId1 = new ActividadId(123);
        $actividadId2 = new ActividadId(456);
        $this->assertFalse($actividadId1->equals($actividadId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $actividadId = ActividadId::fromNullableInt(123);
        $this->assertInstanceOf(ActividadId::class, $actividadId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $actividadId = ActividadId::fromNullableInt(null);
        $this->assertNull($actividadId);
    }

}
