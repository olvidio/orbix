<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\NivelId;
use Tests\myTest;

class NivelIdTest extends myTest
{
    public function test_create_valid_nivelId()
    {
        $nivelId = new NivelId(1234);
        $this->assertEquals(1234, $nivelId->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new NivelId(22222222); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_nivelId()
    {
        $nivelId1 = new NivelId(1234);
        $nivelId2 = new NivelId(1234);
        $this->assertTrue($nivelId1->equals($nivelId2));
    }

    public function test_equals_returns_false_for_different_nivelId()
    {
        $nivelId1 = new NivelId(1234);
        $nivelId2 = new NivelId(2567);
        $this->assertFalse($nivelId1->equals($nivelId2));
    }

    public function test_to_string_returns_nivelId_value()
    {
        $nivelId = new NivelId(1234);
        $this->assertEquals(1234, (string)$nivelId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $nivelId = NivelId::fromNullableInt(1234);
        $this->assertInstanceOf(NivelId::class, $nivelId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $nivelId = NivelId::fromNullableInt(null);
        $this->assertNull($nivelId);
    }

}
