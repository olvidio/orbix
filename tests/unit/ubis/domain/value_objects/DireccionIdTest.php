<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\DireccionId;
use Tests\myTest;

class DireccionIdTest extends myTest
{
    public function test_create_valid_direccionId()
    {
        $direccionId = new DireccionId(300514);
        $this->assertEquals(300514, $direccionId->value());
    }

    public function test_to_string_returns_direccionId_value()
    {
        $direccionId = new DireccionId(300514);
        $this->assertEquals(300514, (string)$direccionId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $direccionId = DireccionId::fromNullableInt(300514);
        $this->assertInstanceOf(DireccionId::class, $direccionId);
    }


}
