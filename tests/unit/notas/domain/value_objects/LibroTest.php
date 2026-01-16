<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Libro;
use Tests\myTest;

class LibroTest extends myTest
{
    public function test_create_valid_libro()
    {
        $libro = new Libro(123);
        $this->assertEquals(123, $libro->value());
    }

    public function test_to_string_returns_libro_value()
    {
        $libro = new Libro(123);
        $this->assertEquals(123, (string)$libro);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $libro = Libro::fromNullableInt(123);
        $this->assertInstanceOf(Libro::class, $libro);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $libro = Libro::fromNullableInt(null);
        $this->assertNull($libro);
    }

}
