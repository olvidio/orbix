<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TipoTelecoId;
use Tests\myTest;

class TipoTelecoIdTest extends myTest
{
    public function test_create_valid_tipoTelecoId()
    {
        $tipoTelecoId = new TipoTelecoId(123);
        $this->assertEquals(123, $tipoTelecoId->value());
    }

    public function test_equals_returns_true_for_same_tipoTelecoId()
    {
        $tipoTelecoId1 = new TipoTelecoId(123);
        $tipoTelecoId2 = new TipoTelecoId(123);
        $this->assertTrue($tipoTelecoId1->equals($tipoTelecoId2));
    }

    public function test_equals_returns_false_for_different_tipoTelecoId()
    {
        $tipoTelecoId1 = new TipoTelecoId(123);
        $tipoTelecoId2 = new TipoTelecoId(456);
        $this->assertFalse($tipoTelecoId1->equals($tipoTelecoId2));
    }

    public function test_to_string_returns_tipoTelecoId_value()
    {
        $tipoTelecoId = new TipoTelecoId(123);
        $this->assertEquals(123, (string)$tipoTelecoId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipoTelecoId = TipoTelecoId::fromNullableInt(123);
        $this->assertInstanceOf(TipoTelecoId::class, $tipoTelecoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tipoTelecoId = TipoTelecoId::fromNullableInt(null);
        $this->assertNull($tipoTelecoId);
    }

}
