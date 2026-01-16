<?php

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\SfsvId;
use Tests\myTest;

class SfsvIdTest extends myTest
{
    public function test_create_valid_sfsvId()
    {
        $sfsvId = new SfsvId(SfsvId::SV);
        $this->assertEquals(1, $sfsvId->value());
    }

    public function test_invalid_sfsvId_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new SfsvId(999);
    }

    public function test_equals_returns_true_for_same_sfsvId()
    {
        $sfsvId1 = new SfsvId(SfsvId::SV);
        $sfsvId2 = new SfsvId(SfsvId::SV);
        $this->assertTrue($sfsvId1->equals($sfsvId2));
    }

    public function test_equals_returns_false_for_different_sfsvId()
    {
        $sfsvId1 = new SfsvId(SfsvId::SV);
        $sfsvId2 = new SfsvId(SfsvId::SF);
        $this->assertFalse($sfsvId1->equals($sfsvId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $sfsvId = SfsvId::fromNullableInt(SfsvId::SV);
        $this->assertInstanceOf(SfsvId::class, $sfsvId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $sfsvId = SfsvId::fromNullableInt(null);
        $this->assertNull($sfsvId);
    }

}
