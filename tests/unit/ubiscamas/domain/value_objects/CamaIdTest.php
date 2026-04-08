<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\CamaId;
use Tests\myTest;

class CamaIdTest extends myTest
{
    public function test_create_valid_camaId()
    {
        $camaId = new CamaId('abc-123');
        $this->assertEquals('abc-123', $camaId->value());
    }

    public function test_empty_string_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CamaId('');
    }

    public function test_equals_returns_true_for_same_value()
    {
        $camaId1 = new CamaId('abc-123');
        $camaId2 = new CamaId('abc-123');
        $this->assertTrue($camaId1->equals($camaId2));
    }

    public function test_equals_returns_false_for_different_value()
    {
        $camaId1 = new CamaId('abc-123');
        $camaId2 = new CamaId('xyz-456');
        $this->assertFalse($camaId1->equals($camaId2));
    }

    public function test_to_string_returns_value()
    {
        $camaId = new CamaId('abc-123');
        $this->assertEquals('abc-123', (string)$camaId);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $camaId = CamaId::fromNullableString('abc-123');
        $this->assertInstanceOf(CamaId::class, $camaId);
        $this->assertEquals('abc-123', $camaId->value());
    }

    public function test_fromNullableString_returns_null_for_null()
    {
        $this->assertNull(CamaId::fromNullableString(null));
    }

    public function test_fromNullableString_returns_null_for_empty_string()
    {
        $this->assertNull(CamaId::fromNullableString(''));
    }
}
