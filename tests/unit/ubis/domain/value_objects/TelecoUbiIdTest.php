<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\TelecoUbiId;
use Tests\myTest;

class TelecoUbiIdTest extends myTest
{
    public function test_create_valid_telecoUbiId()
    {
        $telecoUbiId = new TelecoUbiId(123);
        $this->assertEquals(123, $telecoUbiId->value());
    }

    public function test_equals_returns_true_for_same_telecoUbiId()
    {
        $telecoUbiId1 = new TelecoUbiId(123);
        $telecoUbiId2 = new TelecoUbiId(123);
        $this->assertTrue($telecoUbiId1->equals($telecoUbiId2));
    }

    public function test_equals_returns_false_for_different_telecoUbiId()
    {
        $telecoUbiId1 = new TelecoUbiId(123);
        $telecoUbiId2 = new TelecoUbiId(456);
        $this->assertFalse($telecoUbiId1->equals($telecoUbiId2));
    }

    public function test_to_string_returns_telecoUbiId_value()
    {
        $telecoUbiId = new TelecoUbiId(123);
        $this->assertEquals(123, (string)$telecoUbiId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $telecoUbiId = TelecoUbiId::fromNullableInt(123);
        $this->assertInstanceOf(TelecoUbiId::class, $telecoUbiId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $telecoUbiId = TelecoUbiId::fromNullableInt(null);
        $this->assertNull($telecoUbiId);
    }

}
