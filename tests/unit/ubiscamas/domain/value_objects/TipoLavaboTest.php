<?php

namespace Tests\unit\ubiscamas\domain\value_objects;

use src\ubiscamas\domain\value_objects\TipoLavabo;
use Tests\myTest;

class TipoLavaboTest extends myTest
{
    public function test_create_valid_tipoLavabo()
    {
        $tipo = new TipoLavabo(1);
        $this->assertEquals(1, $tipo->value());
    }

    public function test_all_valid_values()
    {
        foreach ([1, 2, 3, 4] as $val) {
            $tipo = new TipoLavabo($val);
            $this->assertEquals($val, $tipo->value());
        }
    }

    public function test_invalid_value_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoLavabo(99);
    }

    public function test_to_string_returns_value()
    {
        $tipo = new TipoLavabo(2);
        $this->assertEquals('2', (string)$tipo);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipo = TipoLavabo::fromNullableInt(3);
        $this->assertInstanceOf(TipoLavabo::class, $tipo);
        $this->assertEquals(3, $tipo->value());
    }

    public function test_fromNullableInt_returns_null_for_null()
    {
        $this->assertNull(TipoLavabo::fromNullableInt(null));
    }

    public function test_getArrayTipoLavabo_returns_array_with_4_elements()
    {
        $array = TipoLavabo::getArrayTipoLavabo();
        $this->assertIsArray($array);
        $this->assertCount(4, $array);
        $this->assertArrayHasKey(1, $array);
        $this->assertArrayHasKey(4, $array);
    }
}
