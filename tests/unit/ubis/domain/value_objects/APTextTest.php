<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\APText;
use Tests\myTest;

class APTextTest extends myTest
{
    public function test_create_valid_aPText()
    {
        $aPText = new APText('test value');
        $this->assertEquals('test value', $aPText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new APText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $aPText = APText::fromNullableString('test value');
        $this->assertInstanceOf(APText::class, $aPText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $aPText = APText::fromNullableString(null);
        $this->assertNull($aPText);
    }

}
