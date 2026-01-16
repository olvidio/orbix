<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\ObservDireccionText;
use Tests\myTest;

class ObservDireccionTextTest extends myTest
{
    public function test_create_valid_observDireccionText()
    {
        $observDireccionText = new ObservDireccionText('test value');
        $this->assertEquals('test value', $observDireccionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ObservDireccionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $observDireccionText = ObservDireccionText::fromNullableString('test value');
        $this->assertInstanceOf(ObservDireccionText::class, $observDireccionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $observDireccionText = ObservDireccionText::fromNullableString(null);
        $this->assertNull($observDireccionText);
    }

}
