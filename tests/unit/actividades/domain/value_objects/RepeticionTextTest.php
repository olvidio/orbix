<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\RepeticionText;
use Tests\myTest;

class RepeticionTextTest extends myTest
{
    public function test_create_valid_repeticionText()
    {
        $repeticionText = new RepeticionText('test value');
        $this->assertEquals('test value', $repeticionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new RepeticionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_repeticionText()
    {
        $repeticionText1 = new RepeticionText('test value');
        $repeticionText2 = new RepeticionText('test value');
        $this->assertTrue($repeticionText1->equals($repeticionText2));
    }

    public function test_equals_returns_false_for_different_repeticionText()
    {
        $repeticionText1 = new RepeticionText('test value');
        $repeticionText2 = new RepeticionText('alternative value');
        $this->assertFalse($repeticionText1->equals($repeticionText2));
    }

    public function test_to_string_returns_repeticionText_value()
    {
        $repeticionText = new RepeticionText('test value');
        $this->assertEquals('test value', (string)$repeticionText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $repeticionText = RepeticionText::fromNullableString('test value');
        $this->assertInstanceOf(RepeticionText::class, $repeticionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $repeticionText = RepeticionText::fromNullableString(null);
        $this->assertNull($repeticionText);
    }

}
