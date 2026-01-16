<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\CeCurso;
use Tests\myTest;

class CeCursoTest extends myTest
{
    public function test_create_valid_ceCurso()
    {
        $ceCurso = new CeCurso(123);
        $this->assertEquals(123, $ceCurso->value());
    }

    public function test_to_string_returns_ceCurso_value()
    {
        $ceCurso = new CeCurso(123);
        $this->assertEquals(123, (string)$ceCurso);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $ceCurso = CeCurso::fromNullableInt(123);
        $this->assertInstanceOf(CeCurso::class, $ceCurso);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $ceCurso = CeCurso::fromNullableInt(null);
        $this->assertNull($ceCurso);
    }

}
