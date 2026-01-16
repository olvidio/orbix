<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\CentroId;
use Tests\myTest;

class CentroIdTest extends myTest
{
    public function test_create_valid_centroId()
    {
        $centroId = new CentroId(123);
        $this->assertEquals(123, $centroId->value());
    }

    public function test_equals_returns_true_for_same_centroId()
    {
        $centroId1 = new CentroId(123);
        $centroId2 = new CentroId(123);
        $this->assertTrue($centroId1->equals($centroId2));
    }

    public function test_equals_returns_false_for_different_centroId()
    {
        $centroId1 = new CentroId(123);
        $centroId2 = new CentroId(456);
        $this->assertFalse($centroId1->equals($centroId2));
    }

    public function test_to_string_returns_centroId_value()
    {
        $centroId = new CentroId(123);
        $this->assertEquals(123, (string)$centroId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $centroId = CentroId::fromNullableInt(123);
        $this->assertInstanceOf(CentroId::class, $centroId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $centroId = CentroId::fromNullableInt(null);
        $this->assertNull($centroId);
    }

}
