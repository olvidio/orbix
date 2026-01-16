<?php

namespace Tests\unit\asignaturas\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaTipoShortName;
use Tests\myTest;

class AsignaturaTipoShortNameTest extends myTest
{
    public function test_create_valid_asignaturaTipoShortName()
    {
        $asignaturaTipoShortName = new AsignaturaTipoShortName('te');
        $this->assertEquals('te', $asignaturaTipoShortName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AsignaturaTipoShortName(str_repeat('a', 10)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_asignaturaTipoShortName()
    {
        $asignaturaTipoShortName1 = new AsignaturaTipoShortName('te');
        $asignaturaTipoShortName2 = new AsignaturaTipoShortName('te');
        $this->assertTrue($asignaturaTipoShortName1->equals($asignaturaTipoShortName2));
    }

    public function test_equals_returns_false_for_different_asignaturaTipoShortName()
    {
        $asignaturaTipoShortName1 = new AsignaturaTipoShortName('te');
        $asignaturaTipoShortName2 = new AsignaturaTipoShortName('al');
        $this->assertFalse($asignaturaTipoShortName1->equals($asignaturaTipoShortName2));
    }

    public function test_to_string_returns_asignaturaTipoShortName_value()
    {
        $asignaturaTipoShortName = new AsignaturaTipoShortName('te');
        $this->assertEquals('te', (string)$asignaturaTipoShortName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $asignaturaTipoShortName = AsignaturaTipoShortName::fromNullableString('te');
        $this->assertInstanceOf(AsignaturaTipoShortName::class, $asignaturaTipoShortName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $asignaturaTipoShortName = AsignaturaTipoShortName::fromNullableString(null);
        $this->assertNull($asignaturaTipoShortName);
    }

}
