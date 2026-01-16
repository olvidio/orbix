<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\ActividadDescText;
use Tests\myTest;

class ActividadDescTextTest extends myTest
{
    public function test_create_valid_actividadDescText()
    {
        $actividadDescText = new ActividadDescText('test value');
        $this->assertEquals('test value', $actividadDescText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ActividadDescText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_actividadDescText()
    {
        $actividadDescText1 = new ActividadDescText('test value');
        $actividadDescText2 = new ActividadDescText('test value');
        $this->assertTrue($actividadDescText1->equals($actividadDescText2));
    }

    public function test_equals_returns_false_for_different_actividadDescText()
    {
        $actividadDescText1 = new ActividadDescText('test value');
        $actividadDescText2 = new ActividadDescText('alternative value');
        $this->assertFalse($actividadDescText1->equals($actividadDescText2));
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $actividadDescText = ActividadDescText::fromNullableString('test value');
        $this->assertInstanceOf(ActividadDescText::class, $actividadDescText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $actividadDescText = ActividadDescText::fromNullableString(null);
        $this->assertNull($actividadDescText);
    }

}
