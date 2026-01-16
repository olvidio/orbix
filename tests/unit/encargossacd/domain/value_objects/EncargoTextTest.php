<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoText;
use Tests\myTest;

class EncargoTextTest extends myTest
{
    public function test_create_valid_encargoText()
    {
        $encargoText = new EncargoText('test value');
        $this->assertEquals('test value', $encargoText->value());
    }

    public function test_equals_returns_true_for_same_encargoText()
    {
        $encargoText1 = new EncargoText('test value');
        $encargoText2 = new EncargoText('test value');
        $this->assertTrue($encargoText1->equals($encargoText2));
    }

    public function test_equals_returns_false_for_different_encargoText()
    {
        $encargoText1 = new EncargoText('test value');
        $encargoText2 = new EncargoText('alternative value');
        $this->assertFalse($encargoText1->equals($encargoText2));
    }

    public function test_to_string_returns_encargoText_value()
    {
        $encargoText = new EncargoText('test value');
        $this->assertEquals('test value', (string)$encargoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $encargoText = EncargoText::fromNullableString('test value');
        $this->assertInstanceOf(EncargoText::class, $encargoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $encargoText = EncargoText::fromNullableString(null);
        $this->assertNull($encargoText);
    }

}
