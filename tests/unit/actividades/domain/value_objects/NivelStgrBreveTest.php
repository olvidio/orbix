<?php

namespace Tests\unit\actividades\domain\value_objects;

use src\actividades\domain\value_objects\NivelStgrBreve;
use Tests\myTest;

class NivelStgrBreveTest extends myTest
{
    // OJO No puede tener más de 2 caracteres
    public function test_create_valid_nivelStgrBreve()
    {
        $nivelStgrBreve = new NivelStgrBreve('te');
        $this->assertEquals('te', $nivelStgrBreve->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new NivelStgrBreve(str_repeat('a', 5)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_nivelStgrBreve()
    {
        $nivelStgrBreve1 = new NivelStgrBreve('te');
        $nivelStgrBreve2 = new NivelStgrBreve('te');
        $this->assertTrue($nivelStgrBreve1->equals($nivelStgrBreve2));
    }

    public function test_equals_returns_false_for_different_nivelStgrBreve()
    {
        $nivelStgrBreve1 = new NivelStgrBreve('te');
        $nivelStgrBreve2 = new NivelStgrBreve('al');
        $this->assertFalse($nivelStgrBreve1->equals($nivelStgrBreve2));
    }

    public function test_to_string_returns_nivelStgrBreve_value()
    {
        $nivelStgrBreve = new NivelStgrBreve('te');
        $this->assertEquals('te', (string)$nivelStgrBreve);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $nivelStgrBreve = NivelStgrBreve::fromNullableString('te');
        $this->assertInstanceOf(NivelStgrBreve::class, $nivelStgrBreve);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $nivelStgrBreve = NivelStgrBreve::fromNullableString(null);
        $this->assertNull($nivelStgrBreve);
    }

}
