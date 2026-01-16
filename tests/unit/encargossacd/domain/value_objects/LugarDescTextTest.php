<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\LugarDescText;
use Tests\myTest;

class LugarDescTextTest extends myTest
{
    public function test_create_valid_lugarDescText()
    {
        $lugarDescText = new LugarDescText('test value');
        $this->assertEquals('test value', $lugarDescText->value());
    }

    public function test_equals_returns_true_for_same_lugarDescText()
    {
        $lugarDescText1 = new LugarDescText('test value');
        $lugarDescText2 = new LugarDescText('test value');
        $this->assertTrue($lugarDescText1->equals($lugarDescText2));
    }

    public function test_equals_returns_false_for_different_lugarDescText()
    {
        $lugarDescText1 = new LugarDescText('test value');
        $lugarDescText2 = new LugarDescText('alternative value');
        $this->assertFalse($lugarDescText1->equals($lugarDescText2));
    }

    public function test_to_string_returns_lugarDescText_value()
    {
        $lugarDescText = new LugarDescText('test value');
        $this->assertEquals('test value', (string)$lugarDescText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $lugarDescText = LugarDescText::fromNullableString('test value');
        $this->assertInstanceOf(LugarDescText::class, $lugarDescText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $lugarDescText = LugarDescText::fromNullableString(null);
        $this->assertNull($lugarDescText);
    }

}
