<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\MesNum;
use Tests\myTest;

class MesNumTest extends myTest
{
    public function test_create_valid_mesNum()
    {
        $mesNum = new MesNum(123);
        $this->assertEquals(123, $mesNum->value());
    }

    public function test_equals_returns_true_for_same_mesNum()
    {
        $mesNum1 = new MesNum(123);
        $mesNum2 = new MesNum(123);
        $this->assertTrue($mesNum1->equals($mesNum2));
    }

    public function test_equals_returns_false_for_different_mesNum()
    {
        $mesNum1 = new MesNum(123);
        $mesNum2 = new MesNum(456);
        $this->assertFalse($mesNum1->equals($mesNum2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $mesNum = MesNum::fromNullableInt(123);
        $this->assertInstanceOf(MesNum::class, $mesNum);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $mesNum = MesNum::fromNullableInt(null);
        $this->assertNull($mesNum);
    }

}
