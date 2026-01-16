<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoTipoText;
use Tests\myTest;

class EncargoTipoTextTest extends myTest
{
    public function test_create_valid_encargoTipoText()
    {
        $encargoTipoText = new EncargoTipoText('test value');
        $this->assertEquals('test value', $encargoTipoText->value());
    }

    public function test_equals_returns_true_for_same_encargoTipoText()
    {
        $encargoTipoText1 = new EncargoTipoText('test value');
        $encargoTipoText2 = new EncargoTipoText('test value');
        $this->assertTrue($encargoTipoText1->equals($encargoTipoText2));
    }

    public function test_equals_returns_false_for_different_encargoTipoText()
    {
        $encargoTipoText1 = new EncargoTipoText('test value');
        $encargoTipoText2 = new EncargoTipoText('alternative value');
        $this->assertFalse($encargoTipoText1->equals($encargoTipoText2));
    }

    public function test_to_string_returns_encargoTipoText_value()
    {
        $encargoTipoText = new EncargoTipoText('test value');
        $this->assertEquals('test value', (string)$encargoTipoText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $encargoTipoText = EncargoTipoText::fromNullableString('test value');
        $this->assertInstanceOf(EncargoTipoText::class, $encargoTipoText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $encargoTipoText = EncargoTipoText::fromNullableString(null);
        $this->assertNull($encargoTipoText);
    }

}
