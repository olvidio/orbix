<?php

namespace Tests\unit\configuracion\domain\value_objects;

use src\configuracion\domain\value_objects\AppId;
use Tests\myTest;

class AppIdTest extends myTest
{
    public function test_create_valid_appId()
    {
        $appId = new AppId(123);
        $this->assertEquals(123, $appId->value());
    }

    public function test_equals_returns_true_for_same_appId()
    {
        $appId1 = new AppId(123);
        $appId2 = new AppId(123);
        $this->assertTrue($appId1->equals($appId2));
    }

    public function test_equals_returns_false_for_different_appId()
    {
        $appId1 = new AppId(123);
        $appId2 = new AppId(456);
        $this->assertFalse($appId1->equals($appId2));
    }

    public function test_to_string_returns_appId_value()
    {
        $appId = new AppId(123);
        $this->assertEquals(123, (string)$appId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $appId = AppId::fromNullableInt(123);
        $this->assertInstanceOf(AppId::class, $appId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $appId = AppId::fromNullableInt(null);
        $this->assertNull($appId);
    }

}
