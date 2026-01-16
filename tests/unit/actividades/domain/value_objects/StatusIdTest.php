<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\StatusId;
use Tests\myTest;

class StatusIdTest extends myTest
{
    public function test_create_valid_statusId()
    {
        $statusId = new StatusId(StatusId::PROYECTO);
        $this->assertEquals(1, $statusId->value());
    }

    public function test_invalid_statusId_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new StatusId(999);
    }

    public function test_equals_returns_true_for_same_statusId()
    {
        $statusId1 = new StatusId(StatusId::PROYECTO);
        $statusId2 = new StatusId(StatusId::PROYECTO);
        $this->assertTrue($statusId1->equals($statusId2));
    }

    public function test_equals_returns_false_for_different_statusId()
    {
        $statusId1 = new StatusId(StatusId::PROYECTO);
        $statusId2 = new StatusId(StatusId::ACTUAL);
        $this->assertFalse($statusId1->equals($statusId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $statusId = StatusId::fromNullableInt(StatusId::PROYECTO);
        $this->assertInstanceOf(StatusId::class, $statusId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $statusId = StatusId::fromNullableInt(null);
        $this->assertNull($statusId);
    }

}
