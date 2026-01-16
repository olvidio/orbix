<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\NivelStgrId;
use Tests\myTest;

class NivelStgrIdTest extends myTest
{
    public function test_create_valid_nivelStgrId()
    {
        $nivelStgrId = new NivelStgrId(NivelStgrId::B);
        $this->assertEquals(1, $nivelStgrId->value());
    }

    public function test_equals_returns_true_for_same_nivelStgrId()
    {
        $nivelStgrId1 = new NivelStgrId(NivelStgrId::B);
        $nivelStgrId2 = new NivelStgrId(NivelStgrId::B);
        $this->assertTrue($nivelStgrId1->equals($nivelStgrId2));
    }

    public function test_equals_returns_false_for_different_nivelStgrId()
    {
        $nivelStgrId1 = new NivelStgrId(NivelStgrId::B);
        $nivelStgrId2 = new NivelStgrId(NivelStgrId::C1);
        $this->assertFalse($nivelStgrId1->equals($nivelStgrId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $nivelStgrId = NivelStgrId::fromNullableInt(NivelStgrId::B);
        $this->assertInstanceOf(NivelStgrId::class, $nivelStgrId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $nivelStgrId = NivelStgrId::fromNullableInt(null);
        $this->assertNull($nivelStgrId);
    }

}
