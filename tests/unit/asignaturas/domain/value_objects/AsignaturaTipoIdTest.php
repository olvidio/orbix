<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaTipoId;
use Tests\myTest;

class AsignaturaTipoIdTest extends myTest
{
    public function test_create_valid_asignaturaTipoId()
    {
        $asignaturaTipoId = new AsignaturaTipoId(3);
        $this->assertEquals(3, $asignaturaTipoId->value());
    }

    public function test_equals_returns_true_for_same_asignaturaTipoId()
    {
        $asignaturaTipoId1 = new AsignaturaTipoId(3);
        $asignaturaTipoId2 = new AsignaturaTipoId(3);
        $this->assertTrue($asignaturaTipoId1->equals($asignaturaTipoId2));
    }

    public function test_equals_returns_false_for_different_asignaturaTipoId()
    {
        $asignaturaTipoId1 = new AsignaturaTipoId(3);
        $asignaturaTipoId2 = new AsignaturaTipoId(4);
        $this->assertFalse($asignaturaTipoId1->equals($asignaturaTipoId2));
    }

    public function test_to_string_returns_asignaturaTipoId_value()
    {
        $asignaturaTipoId = new AsignaturaTipoId(3);
        $this->assertEquals(3, (string)$asignaturaTipoId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $asignaturaTipoId = AsignaturaTipoId::fromNullableInt(3);
        $this->assertInstanceOf(AsignaturaTipoId::class, $asignaturaTipoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $asignaturaTipoId = AsignaturaTipoId::fromNullableInt(null);
        $this->assertNull($asignaturaTipoId);
    }

}
