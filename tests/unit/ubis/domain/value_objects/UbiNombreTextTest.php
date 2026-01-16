<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\UbiNombreText;
use Tests\myTest;

class UbiNombreTextTest extends myTest
{
    public function test_create_valid_ubiNombreText()
    {
        $ubiNombreText = new UbiNombreText('test value');
        $this->assertEquals('test value', $ubiNombreText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UbiNombreText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_ubiNombreText()
    {
        $ubiNombreText1 = new UbiNombreText('test value');
        $ubiNombreText2 = new UbiNombreText('test value');
        $this->assertTrue($ubiNombreText1->equals($ubiNombreText2));
    }

    public function test_equals_returns_false_for_different_ubiNombreText()
    {
        $ubiNombreText1 = new UbiNombreText('test value');
        $ubiNombreText2 = new UbiNombreText('alternative value');
        $this->assertFalse($ubiNombreText1->equals($ubiNombreText2));
    }

    public function test_to_string_returns_ubiNombreText_value()
    {
        $ubiNombreText = new UbiNombreText('test value');
        $this->assertEquals('test value', (string)$ubiNombreText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $ubiNombreText = UbiNombreText::fromNullableString('test value');
        $this->assertInstanceOf(UbiNombreText::class, $ubiNombreText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $ubiNombreText = UbiNombreText::fromNullableString(null);
        $this->assertNull($ubiNombreText);
    }

}
