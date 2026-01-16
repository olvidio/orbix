<?php

namespace Tests\unit\cartaspresentacion\domain\value_objects;

use src\cartaspresentacion\domain\value_objects\PresObservText;
use Tests\myTest;

class PresObservTextTest extends myTest
{
    public function test_create_valid_presObservText()
    {
        $presObservText = new PresObservText('test value');
        $this->assertEquals('test value', $presObservText->value());
    }

    public function test_to_string_returns_presObservText_value()
    {
        $presObservText = new PresObservText('test value');
        $this->assertEquals('test value', (string)$presObservText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $presObservText = PresObservText::fromNullableString('test value');
        $this->assertInstanceOf(PresObservText::class, $presObservText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $presObservText = PresObservText::fromNullableString(null);
        $this->assertNull($presObservText);
    }

}
