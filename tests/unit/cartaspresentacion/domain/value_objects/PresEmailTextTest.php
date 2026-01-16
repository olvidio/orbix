<?php

namespace Tests\unit\cartaspresentacion\domain\value_objects;

use src\cartaspresentacion\domain\value_objects\PresEmailText;
use Tests\myTest;

class PresEmailTextTest extends myTest
{
    public function test_create_valid_presEmailText()
    {
        $presEmailText = new PresEmailText('test@example.com');
        $this->assertEquals('test@example.com', $presEmailText->value());
    }

    public function test_invalid_email_format_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PresEmailText('invalid-email');
    }

    public function test_to_string_returns_presEmailText_value()
    {
        $presEmailText = new PresEmailText('test@example.com');
        $this->assertEquals('test@example.com', (string)$presEmailText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $presEmailText = PresEmailText::fromNullableString('test@example.com');
        $this->assertInstanceOf(PresEmailText::class, $presEmailText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $presEmailText = PresEmailText::fromNullableString(null);
        $this->assertNull($presEmailText);
    }

}
