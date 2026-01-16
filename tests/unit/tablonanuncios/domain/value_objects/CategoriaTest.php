<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use src\tablonanuncios\domain\value_objects\Categoria;
use Tests\myTest;

class CategoriaTest extends myTest
{
    public function test_create_valid_categoria()
    {
        $categoria = new Categoria(Categoria::CAT_ALERTA);
        $this->assertEquals(1, $categoria->value());
    }

    public function test_invalid_categoria_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Categoria(999);
    }

    public function test_equals_returns_true_for_same_categoria()
    {
        $categoria1 = new Categoria(Categoria::CAT_ALERTA);
        $categoria2 = new Categoria(Categoria::CAT_ALERTA);
        $this->assertTrue($categoria1->equals($categoria2));
    }

    public function test_equals_returns_false_for_different_categoria()
    {
        $categoria1 = new Categoria(Categoria::CAT_ALERTA);
        $categoria2 = new Categoria(Categoria::CAT_AVISO);
        $this->assertFalse($categoria1->equals($categoria2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $categoria = Categoria::fromNullableInt(Categoria::CAT_ALERTA);
        $this->assertInstanceOf(Categoria::class, $categoria);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $categoria = Categoria::fromNullableInt(null);
        $this->assertNull($categoria);
    }

}
