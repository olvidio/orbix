<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoModoId;
use Tests\myTest;

class EncargoModoIdTest extends myTest
{
    public function test_create_valid_encargoModoId()
    {
        $encargoModoId = new EncargoModoId(123);
        $this->assertEquals(123, $encargoModoId->value());
    }

    public function test_equals_returns_true_for_same_encargoModoId()
    {
        $encargoModoId1 = new EncargoModoId(123);
        $encargoModoId2 = new EncargoModoId(123);
        $this->assertTrue($encargoModoId1->equals($encargoModoId2));
    }

    public function test_equals_returns_false_for_different_encargoModoId()
    {
        $encargoModoId1 = new EncargoModoId(123);
        $encargoModoId2 = new EncargoModoId(456);
        $this->assertFalse($encargoModoId1->equals($encargoModoId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $encargoModoId = EncargoModoId::fromNullableInt(123);
        $this->assertInstanceOf(EncargoModoId::class, $encargoModoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $encargoModoId = EncargoModoId::fromNullableInt(null);
        $this->assertNull($encargoModoId);
    }

}
