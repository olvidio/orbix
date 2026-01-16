<?php

namespace Tests\unit\ubis\domain\value_objects;

use src\ubis\domain\value_objects\PlanoExtensionText;
use Tests\myTest;

class PlanoExtensionTextTest extends myTest
{
    public function test_create_valid_planoExtensionText()
    {
        $planoExtensionText = new PlanoExtensionText('test_value');
        $this->assertEquals('test_value', $planoExtensionText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PlanoExtensionText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $planoExtensionText = PlanoExtensionText::fromNullableString('test_value');
        $this->assertInstanceOf(PlanoExtensionText::class, $planoExtensionText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $planoExtensionText = PlanoExtensionText::fromNullableString(null);
        $this->assertNull($planoExtensionText);
    }

}
