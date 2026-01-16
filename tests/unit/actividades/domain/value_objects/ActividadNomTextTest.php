<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\ActividadNomText;
use Tests\myTest;

class ActividadNomTextTest extends myTest
{
    public function test_create_valid_actividadNomText()
    {
        $actividadNomText = new ActividadNomText('test value');
        $this->assertEquals('test value', $actividadNomText->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ActividadNomText(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_actividadNomText()
    {
        $actividadNomText1 = new ActividadNomText('test value');
        $actividadNomText2 = new ActividadNomText('test value');
        $this->assertTrue($actividadNomText1->equals($actividadNomText2));
    }

    public function test_equals_returns_false_for_different_actividadNomText()
    {
        $actividadNomText1 = new ActividadNomText('test value');
        $actividadNomText2 = new ActividadNomText('alternative value');
        $this->assertFalse($actividadNomText1->equals($actividadNomText2));
    }

    public function test_to_string_returns_actividadNomText_value()
    {
        $actividadNomText = new ActividadNomText('test value');
        $this->assertEquals('test value', (string)$actividadNomText);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $actividadNomText = ActividadNomText::fromNullableString('test value');
        $this->assertInstanceOf(ActividadNomText::class, $actividadNomText);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $actividadNomText = ActividadNomText::fromNullableString(null);
        $this->assertNull($actividadNomText);
    }

}
