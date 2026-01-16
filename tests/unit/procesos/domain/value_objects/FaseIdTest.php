<?php

namespace Tests\unit\procesos\domain\value_objects;

use src\procesos\domain\value_objects\FaseId;
use Tests\myTest;

class FaseIdTest extends myTest
{
    public function test_create_valid_faseId()
    {
        $faseId = new FaseId(FaseId::FASE_PROYECTO);
        $this->assertEquals(1, $faseId->value());
    }

    public function test_equals_returns_true_for_same_faseId()
    {
        $faseId1 = new FaseId(FaseId::FASE_PROYECTO);
        $faseId2 = new FaseId(FaseId::FASE_PROYECTO);
        $this->assertTrue($faseId1->equals($faseId2));
    }

    public function test_equals_returns_false_for_different_faseId()
    {
        $faseId1 = new FaseId(FaseId::FASE_PROYECTO);
        $faseId2 = new FaseId(FaseId::FASE_APROBADA);
        $this->assertFalse($faseId1->equals($faseId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $faseId = FaseId::fromNullableInt(FaseId::FASE_PROYECTO);
        $this->assertInstanceOf(FaseId::class, $faseId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $faseId = FaseId::fromNullableInt(null);
        $this->assertNull($faseId);
    }

}
