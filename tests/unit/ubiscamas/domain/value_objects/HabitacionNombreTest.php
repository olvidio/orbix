<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\HabitacionNombre;
use Tests\myTest;

class HabitacionNombreTest extends myTest
{
    public function test_create_valid_habitacionNombre()
    {
        $nombre = new HabitacionNombre('Habitación 101');
        $this->assertEquals('Habitación 101', $nombre->value());
    }

    public function test_to_string_returns_value()
    {
        $nombre = new HabitacionNombre('Habitación 101');
        $this->assertEquals('Habitación 101', (string)$nombre);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $nombre = HabitacionNombre::fromNullableString('Habitación 101');
        $this->assertInstanceOf(HabitacionNombre::class, $nombre);
        $this->assertEquals('Habitación 101', $nombre->value());
    }

    public function test_fromNullableString_returns_null_for_null()
    {
        $this->assertNull(HabitacionNombre::fromNullableString(null));
    }

    public function test_fromNullableString_returns_null_for_empty_string()
    {
        $this->assertNull(HabitacionNombre::fromNullableString(''));
    }
}
