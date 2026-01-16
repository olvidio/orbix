<?php

namespace Tests\unit\actividadestudios\domain\value_objects;

use src\actividadestudios\domain\value_objects\NotaMax;
use Tests\myTest;

class NotaMaxTest extends myTest
{
    public function test_create_valid_notaMax()
    {
        $notaMax = new NotaMax(123);
        $this->assertEquals(123, $notaMax->value());
    }

    public function test_to_string_returns_notaMax_value()
    {
        $notaMax = new NotaMax(123);
        $this->assertEquals(123, (string)$notaMax);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $notaMax = NotaMax::fromNullableInt(123);
        $this->assertInstanceOf(NotaMax::class, $notaMax);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $notaMax = NotaMax::fromNullableInt(null);
        $this->assertNull($notaMax);
    }

}
