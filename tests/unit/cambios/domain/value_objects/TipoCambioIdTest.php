<?php

namespace Tests\unit\cambios\domain\value_objects;

use src\cambios\domain\value_objects\TipoCambioId;
use Tests\myTest;

class TipoCambioIdTest extends myTest
{
    public function test_create_valid_tipoCambioId()
    {
        $tipoCambioId = new TipoCambioId(TipoCambioId::INSERT);
        $this->assertEquals(1, $tipoCambioId->value());
    }

    public function test_invalid_tipoCambioId_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoCambioId(999);
    }

    public function test_equals_returns_true_for_same_tipoCambioId()
    {
        $tipoCambioId1 = new TipoCambioId(TipoCambioId::INSERT);
        $tipoCambioId2 = new TipoCambioId(TipoCambioId::INSERT);
        $this->assertTrue($tipoCambioId1->equals($tipoCambioId2));
    }

    public function test_equals_returns_false_for_different_tipoCambioId()
    {
        $tipoCambioId1 = new TipoCambioId(TipoCambioId::INSERT);
        $tipoCambioId2 = new TipoCambioId(TipoCambioId::UPDATE);
        $this->assertFalse($tipoCambioId1->equals($tipoCambioId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipoCambioId = TipoCambioId::fromNullableInt(TipoCambioId::INSERT);
        $this->assertInstanceOf(TipoCambioId::class, $tipoCambioId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tipoCambioId = TipoCambioId::fromNullableInt(null);
        $this->assertNull($tipoCambioId);
    }

}
