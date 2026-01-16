<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\EapText;
use Tests\myTest;

class EapTextTest extends myTest
{
    public function test_create_valid_eapText()
    {
        $eapText = new EapText('test value');
        $this->assertEquals('test value', $eapText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EapText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_eapText_value()
    {
        $eapText = new EapText('test value');
        $this->assertEquals('test value', (string)$eapText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $eapText = EapText::fromNullableString('test value');
        $this->assertInstanceOf(EapText::class, $eapText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $eapText = EapText::fromNullableString(null);
        $this->assertNull($eapText);
    }

}
