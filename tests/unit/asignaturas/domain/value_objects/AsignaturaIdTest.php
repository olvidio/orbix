<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaId;
use Tests\myTest;

class AsignaturaIdTest extends myTest
{
    // OJO AsignaturaId must be a 4-digit integer starting with 1, 2 or 3, or be 9998/9999
    public function test_create_valid_asignaturaId()
    {
        $asignaturaId = new AsignaturaId(1234);
        $this->assertEquals(1234, $asignaturaId->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsignaturaId(str_repeat(2, 10)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_asignaturaId()
    {
        $asignaturaId1 = new AsignaturaId(1234);
        $asignaturaId2 = new AsignaturaId(1234);
        $this->assertTrue($asignaturaId1->equals($asignaturaId2));
    }

    public function test_equals_returns_false_for_different_asignaturaId()
    {
        $asignaturaId1 = new AsignaturaId(1234);
        $asignaturaId2 = new AsignaturaId(2567);
        $this->assertFalse($asignaturaId1->equals($asignaturaId2));
    }

    public function test_to_string_returns_asignaturaId_value()
    {
        $asignaturaId = new AsignaturaId(1234);
        $this->assertEquals(1234, (string)$asignaturaId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $asignaturaId = AsignaturaId::fromNullableInt(1234);
        $this->assertInstanceOf(AsignaturaId::class, $asignaturaId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $asignaturaId = AsignaturaId::fromNullableInt(null);
        $this->assertNull($asignaturaId);
    }

}
