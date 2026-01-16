<?php

namespace Tests\unit\cartaspresentacion\domain\value_objects;

use src\cartaspresentacion\domain\value_objects\PresZonaText;
use Tests\myTest;

class PresZonaTextTest extends myTest
{
    public function test_create_valid_presZonaText()
    {
        $presZonaText = new PresZonaText('test value');
        $this->assertEquals('test value', $presZonaText->value());
    }

    public function test_to_string_returns_presZonaText_value()
    {
        $presZonaText = new PresZonaText('test value');
        $this->assertEquals('test value', (string)$presZonaText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $presZonaText = PresZonaText::fromNullableString('test value');
        $this->assertInstanceOf(PresZonaText::class, $presZonaText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $presZonaText = PresZonaText::fromNullableString(null);
        $this->assertNull($presZonaText);
    }

}
