<?php

namespace Tests\unit\zonassacd\domain\value_objects;

use src\zonassacd\domain\value_objects\NombreZona;
use Tests\myTest;

class NombreZonaTest extends myTest
{
    public function test_create_valid_nombreZona()
    {
        $nombreZona = new NombreZona('test value');
        $this->assertEquals('test value', $nombreZona->value());
    }

    public function test_empty_nombreZona_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new NombreZona('');
    }

    public function test_equals_returns_true_for_same_nombreZona()
    {
        $nombreZona1 = new NombreZona('test value');
        $nombreZona2 = new NombreZona('test value');
        $this->assertTrue($nombreZona1->equals($nombreZona2));
    }

    public function test_equals_returns_false_for_different_nombreZona()
    {
        $nombreZona1 = new NombreZona('test value');
        $nombreZona2 = new NombreZona('alternative value');
        $this->assertFalse($nombreZona1->equals($nombreZona2));
    }

    public function test_to_string_returns_nombreZona_value()
    {
        $nombreZona = new NombreZona('test value');
        $this->assertEquals('test value', (string)$nombreZona);
    }

}
