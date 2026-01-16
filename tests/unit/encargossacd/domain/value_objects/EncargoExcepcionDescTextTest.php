<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoExcepcionDescText;
use Tests\myTest;

class EncargoExcepcionDescTextTest extends myTest
{
    public function test_create_valid_encargoExcepcionDescText()
    {
        $encargoExcepcionDescText = new EncargoExcepcionDescText('test value');
        $this->assertEquals('test value', $encargoExcepcionDescText->value());
    }

    public function test_equals_returns_true_for_same_encargoExcepcionDescText()
    {
        $encargoExcepcionDescText1 = new EncargoExcepcionDescText('test value');
        $encargoExcepcionDescText2 = new EncargoExcepcionDescText('test value');
        $this->assertTrue($encargoExcepcionDescText1->equals($encargoExcepcionDescText2));
    }

    public function test_equals_returns_false_for_different_encargoExcepcionDescText()
    {
        $encargoExcepcionDescText1 = new EncargoExcepcionDescText('test value');
        $encargoExcepcionDescText2 = new EncargoExcepcionDescText('alternative value');
        $this->assertFalse($encargoExcepcionDescText1->equals($encargoExcepcionDescText2));
    }

    public function test_to_string_returns_encargoExcepcionDescText_value()
    {
        $encargoExcepcionDescText = new EncargoExcepcionDescText('test value');
        $this->assertEquals('test value', (string)$encargoExcepcionDescText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $encargoExcepcionDescText = EncargoExcepcionDescText::fromNullableString('test value');
        $this->assertInstanceOf(EncargoExcepcionDescText::class, $encargoExcepcionDescText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $encargoExcepcionDescText = EncargoExcepcionDescText::fromNullableString(null);
        $this->assertNull($encargoExcepcionDescText);
    }

}
