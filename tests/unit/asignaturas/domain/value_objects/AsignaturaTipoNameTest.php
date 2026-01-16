<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaTipoName;
use Tests\myTest;

class AsignaturaTipoNameTest extends myTest
{
    public function test_create_valid_asignaturaTipoName()
    {
        $asignaturaTipoName = new AsignaturaTipoName('test value');
        $this->assertEquals('test value', $asignaturaTipoName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsignaturaTipoName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_asignaturaTipoName()
    {
        $asignaturaTipoName1 = new AsignaturaTipoName('test value');
        $asignaturaTipoName2 = new AsignaturaTipoName('test value');
        $this->assertTrue($asignaturaTipoName1->equals($asignaturaTipoName2));
    }

    public function test_equals_returns_false_for_different_asignaturaTipoName()
    {
        $asignaturaTipoName1 = new AsignaturaTipoName('test value');
        $asignaturaTipoName2 = new AsignaturaTipoName('alternative value');
        $this->assertFalse($asignaturaTipoName1->equals($asignaturaTipoName2));
    }

    public function test_to_string_returns_asignaturaTipoName_value()
    {
        $asignaturaTipoName = new AsignaturaTipoName('test value');
        $this->assertEquals('test value', (string)$asignaturaTipoName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asignaturaTipoName = AsignaturaTipoName::fromNullableString('test value');
        $this->assertInstanceOf(AsignaturaTipoName::class, $asignaturaTipoName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asignaturaTipoName = AsignaturaTipoName::fromNullableString(null);
        $this->assertNull($asignaturaTipoName);
    }

}
