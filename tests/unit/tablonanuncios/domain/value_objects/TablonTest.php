<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use src\tablonanuncios\domain\value_objects\Tablon;
use Tests\myTest;

class TablonTest extends myTest
{
    public function test_create_valid_tablon()
    {
        $tablon = new Tablon('test value');
        $this->assertEquals('test value', $tablon->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Tablon(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_equals_returns_true_for_same_tablon()
    {
        $tablon1 = new Tablon('test value');
        $tablon2 = new Tablon('test value');
        $this->assertTrue($tablon1->equals($tablon2));
    }

    public function test_equals_returns_false_for_different_tablon()
    {
        $tablon1 = new Tablon('test value');
        $tablon2 = new Tablon('alternative value');
        $this->assertFalse($tablon1->equals($tablon2));
    }

    public function test_to_string_returns_tablon_value()
    {
        $tablon = new Tablon('test value');
        $this->assertEquals('test value', (string)$tablon);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $tablon = Tablon::fromNullableString('test value');
        $this->assertInstanceOf(Tablon::class, $tablon);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $tablon = Tablon::fromNullableString(null);
        $this->assertNull($tablon);
    }

}
