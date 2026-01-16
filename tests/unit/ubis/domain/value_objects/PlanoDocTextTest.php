<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\PlanoDocText;
use Tests\myTest;

class PlanoDocTextTest extends myTest
{
    public function test_create_valid_planoDocText()
    {
        $planoDocText = new PlanoDocText('test value');
        $this->assertEquals('test value', $planoDocText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PlanoDocText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $planoDocText = PlanoDocText::fromNullableString('test value');
        $this->assertInstanceOf(PlanoDocText::class, $planoDocText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $planoDocText = PlanoDocText::fromNullableString(null);
        $this->assertNull($planoDocText);
    }

}
