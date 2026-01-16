<?php

namespace Tests\unit\cartaspresentacion\domain\value_objects;

use src\cartaspresentacion\domain\value_objects\PresNombreText;
use Tests\myTest;

class PresNombreTextTest extends myTest
{
    public function test_create_valid_presNombreText()
    {
        $presNombreText = new PresNombreText('test value');
        $this->assertEquals('test value', $presNombreText->value());
    }

    public function test_to_string_returns_presNombreText_value()
    {
        $presNombreText = new PresNombreText('test value');
        $this->assertEquals('test value', (string)$presNombreText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $presNombreText = PresNombreText::fromNullableString('test value');
        $this->assertInstanceOf(PresNombreText::class, $presNombreText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $presNombreText = PresNombreText::fromNullableString(null);
        $this->assertNull($presNombreText);
    }

}
