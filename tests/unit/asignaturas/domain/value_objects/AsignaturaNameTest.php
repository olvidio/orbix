<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaName;
use Tests\myTest;

class AsignaturaNameTest extends myTest
{
    public function test_create_valid_asignaturaName()
    {
        $asignaturaName = new AsignaturaName('test value');
        $this->assertEquals('test value', $asignaturaName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsignaturaName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_asignaturaName()
    {
        $asignaturaName1 = new AsignaturaName('test value');
        $asignaturaName2 = new AsignaturaName('test value');
        $this->assertTrue($asignaturaName1->equals($asignaturaName2));
    }

    public function test_equals_returns_false_for_different_asignaturaName()
    {
        $asignaturaName1 = new AsignaturaName('test value');
        $asignaturaName2 = new AsignaturaName('alternative value');
        $this->assertFalse($asignaturaName1->equals($asignaturaName2));
    }

    public function test_to_string_returns_asignaturaName_value()
    {
        $asignaturaName = new AsignaturaName('test value');
        $this->assertEquals('test value', (string)$asignaturaName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asignaturaName = AsignaturaName::fromNullableString('test value');
        $this->assertInstanceOf(AsignaturaName::class, $asignaturaName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asignaturaName = AsignaturaName::fromNullableString(null);
        $this->assertNull($asignaturaName);
    }

}
