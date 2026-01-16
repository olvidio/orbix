<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\ObservCasaText;
use Tests\myTest;

class ObservCasaTextTest extends myTest
{
    public function test_create_valid_observCasaText()
    {
        $observCasaText = new ObservCasaText('test value');
        $this->assertEquals('test value', $observCasaText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ObservCasaText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_observCasaText()
    {
        $observCasaText1 = new ObservCasaText('test value');
        $observCasaText2 = new ObservCasaText('test value');
        $this->assertTrue($observCasaText1->equals($observCasaText2));
    }

    public function test_equals_returns_false_for_different_observCasaText()
    {
        $observCasaText1 = new ObservCasaText('test value');
        $observCasaText2 = new ObservCasaText('alternative value');
        $this->assertFalse($observCasaText1->equals($observCasaText2));
    }

    public function test_to_string_returns_observCasaText_value()
    {
        $observCasaText = new ObservCasaText('test value');
        $this->assertEquals('test value', (string)$observCasaText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observCasaText = ObservCasaText::fromNullableString('test value');
        $this->assertInstanceOf(ObservCasaText::class, $observCasaText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observCasaText = ObservCasaText::fromNullableString(null);
        $this->assertNull($observCasaText);
    }

}
