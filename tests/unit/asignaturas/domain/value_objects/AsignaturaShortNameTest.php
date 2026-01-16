<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaShortName;
use Tests\myTest;

class AsignaturaShortNameTest extends myTest
{
    public function test_create_valid_asignaturaShortName()
    {
        $asignaturaShortName = new AsignaturaShortName('test value');
        $this->assertEquals('test value', $asignaturaShortName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsignaturaShortName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_asignaturaShortName()
    {
        $asignaturaShortName1 = new AsignaturaShortName('test value');
        $asignaturaShortName2 = new AsignaturaShortName('test value');
        $this->assertTrue($asignaturaShortName1->equals($asignaturaShortName2));
    }

    public function test_equals_returns_false_for_different_asignaturaShortName()
    {
        $asignaturaShortName1 = new AsignaturaShortName('test value');
        $asignaturaShortName2 = new AsignaturaShortName('alternative value');
        $this->assertFalse($asignaturaShortName1->equals($asignaturaShortName2));
    }

    public function test_to_string_returns_asignaturaShortName_value()
    {
        $asignaturaShortName = new AsignaturaShortName('test value');
        $this->assertEquals('test value', (string)$asignaturaShortName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asignaturaShortName = AsignaturaShortName::fromNullableString('test value');
        $this->assertInstanceOf(AsignaturaShortName::class, $asignaturaShortName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asignaturaShortName = AsignaturaShortName::fromNullableString(null);
        $this->assertNull($asignaturaShortName);
    }

}
