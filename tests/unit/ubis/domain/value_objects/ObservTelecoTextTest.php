<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\ObservTelecoText;
use Tests\myTest;

class ObservTelecoTextTest extends myTest
{
    public function test_create_valid_observTelecoText()
    {
        $observTelecoText = new ObservTelecoText('test value');
        $this->assertEquals('test value', $observTelecoText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ObservTelecoText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_observTelecoText()
    {
        $observTelecoText1 = new ObservTelecoText('test value');
        $observTelecoText2 = new ObservTelecoText('test value');
        $this->assertTrue($observTelecoText1->equals($observTelecoText2));
    }

    public function test_equals_returns_false_for_different_observTelecoText()
    {
        $observTelecoText1 = new ObservTelecoText('test value');
        $observTelecoText2 = new ObservTelecoText('alternative value');
        $this->assertFalse($observTelecoText1->equals($observTelecoText2));
    }

    public function test_to_string_returns_observTelecoText_value()
    {
        $observTelecoText = new ObservTelecoText('test value');
        $this->assertEquals('test value', (string)$observTelecoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observTelecoText = ObservTelecoText::fromNullableString('test value');
        $this->assertInstanceOf(ObservTelecoText::class, $observTelecoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observTelecoText = ObservTelecoText::fromNullableString(null);
        $this->assertNull($observTelecoText);
    }

}
