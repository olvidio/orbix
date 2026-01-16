<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\Pagina;
use Tests\myTest;

class PaginaTest extends myTest
{
    public function test_create_valid_pagina()
    {
        $pagina = new Pagina(123);
        $this->assertEquals(123, $pagina->value());
    }

    public function test_to_string_returns_pagina_value()
    {
        $pagina = new Pagina(123);
        $this->assertEquals(123, (string)$pagina);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $pagina = Pagina::fromNullableInt(123);
        $this->assertInstanceOf(Pagina::class, $pagina);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $pagina = Pagina::fromNullableInt(null);
        $this->assertNull($pagina);
    }

}
