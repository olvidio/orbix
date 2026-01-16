<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\ObservText;
use Tests\myTest;

class ObservTextTest extends myTest
{
    public function test_create_valid_observText()
    {
        $observText = new ObservText('test value');
        $this->assertEquals('test value', $observText->value());
    }

    public function test_equals_returns_true_for_same_observText()
    {
        $observText1 = new ObservText('test value');
        $observText2 = new ObservText('test value');
        $this->assertTrue($observText1->equals($observText2));
    }

    public function test_equals_returns_false_for_different_observText()
    {
        $observText1 = new ObservText('test value');
        $observText2 = new ObservText('alternative value');
        $this->assertFalse($observText1->equals($observText2));
    }

    public function test_to_string_returns_observText_value()
    {
        $observText = new ObservText('test value');
        $this->assertEquals('test value', (string)$observText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observText = ObservText::fromNullableString('test value');
        $this->assertInstanceOf(ObservText::class, $observText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observText = ObservText::fromNullableString(null);
        $this->assertNull($observText);
    }

}
