<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoDescText;
use Tests\myTest;

class EncargoDescTextTest extends myTest
{
    public function test_create_valid_encargoDescText()
    {
        $encargoDescText = new EncargoDescText('test value');
        $this->assertEquals('test value', $encargoDescText->value());
    }

    public function test_equals_returns_true_for_same_encargoDescText()
    {
        $encargoDescText1 = new EncargoDescText('test value');
        $encargoDescText2 = new EncargoDescText('test value');
        $this->assertTrue($encargoDescText1->equals($encargoDescText2));
    }

    public function test_equals_returns_false_for_different_encargoDescText()
    {
        $encargoDescText1 = new EncargoDescText('test value');
        $encargoDescText2 = new EncargoDescText('alternative value');
        $this->assertFalse($encargoDescText1->equals($encargoDescText2));
    }

    public function test_to_string_returns_encargoDescText_value()
    {
        $encargoDescText = new EncargoDescText('test value');
        $this->assertEquals('test value', (string)$encargoDescText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $encargoDescText = EncargoDescText::fromNullableString('test value');
        $this->assertInstanceOf(EncargoDescText::class, $encargoDescText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $encargoDescText = EncargoDescText::fromNullableString(null);
        $this->assertNull($encargoDescText);
    }

}
