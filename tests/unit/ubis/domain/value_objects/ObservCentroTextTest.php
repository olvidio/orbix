<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\ObservCentroText;
use Tests\myTest;

class ObservCentroTextTest extends myTest
{
    public function test_create_valid_observCentroText()
    {
        $observCentroText = new ObservCentroText('test value');
        $this->assertEquals('test value', $observCentroText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ObservCentroText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_observCentroText()
    {
        $observCentroText1 = new ObservCentroText('test value');
        $observCentroText2 = new ObservCentroText('test value');
        $this->assertTrue($observCentroText1->equals($observCentroText2));
    }

    public function test_equals_returns_false_for_different_observCentroText()
    {
        $observCentroText1 = new ObservCentroText('test value');
        $observCentroText2 = new ObservCentroText('alternative value');
        $this->assertFalse($observCentroText1->equals($observCentroText2));
    }

    public function test_to_string_returns_observCentroText_value()
    {
        $observCentroText = new ObservCentroText('test value');
        $this->assertEquals('test value', (string)$observCentroText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observCentroText = ObservCentroText::fromNullableString('test value');
        $this->assertInstanceOf(ObservCentroText::class, $observCentroText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observCentroText = ObservCentroText::fromNullableString(null);
        $this->assertNull($observCentroText);
    }

}
