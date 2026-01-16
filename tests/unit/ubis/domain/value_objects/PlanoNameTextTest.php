<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\PlanoNameText;
use Tests\myTest;

class PlanoNameTextTest extends myTest
{
    public function test_create_valid_planoNameText()
    {
        $planoNameText = new PlanoNameText('test value');
        $this->assertEquals('test value', $planoNameText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PlanoNameText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $planoNameText = PlanoNameText::fromNullableString('test value');
        $this->assertInstanceOf(PlanoNameText::class, $planoNameText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $planoNameText = PlanoNameText::fromNullableString(null);
        $this->assertNull($planoNameText);
    }

}
