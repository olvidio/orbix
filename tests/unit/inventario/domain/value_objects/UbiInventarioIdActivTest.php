<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\UbiInventarioIdActiv;
use Tests\myTest;

class UbiInventarioIdActivTest extends myTest
{
    public function test_create_valid_ubiInventarioIdActiv()
    {
        $ubiInventarioIdActiv = new UbiInventarioIdActiv(123);
        $this->assertEquals(123, $ubiInventarioIdActiv->value());
    }

    public function test_equals_returns_true_for_same_ubiInventarioIdActiv()
    {
        $ubiInventarioIdActiv1 = new UbiInventarioIdActiv(123);
        $ubiInventarioIdActiv2 = new UbiInventarioIdActiv(123);
        $this->assertTrue($ubiInventarioIdActiv1->equals($ubiInventarioIdActiv2));
    }

    public function test_equals_returns_false_for_different_ubiInventarioIdActiv()
    {
        $ubiInventarioIdActiv1 = new UbiInventarioIdActiv(123);
        $ubiInventarioIdActiv2 = new UbiInventarioIdActiv(456);
        $this->assertFalse($ubiInventarioIdActiv1->equals($ubiInventarioIdActiv2));
    }

    public function test_to_string_returns_ubiInventarioIdActiv_value()
    {
        $ubiInventarioIdActiv = new UbiInventarioIdActiv(123);
        $this->assertEquals(123, (string)$ubiInventarioIdActiv);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $ubiInventarioIdActiv = UbiInventarioIdActiv::fromNullableInt(123);
        $this->assertInstanceOf(UbiInventarioIdActiv::class, $ubiInventarioIdActiv);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $ubiInventarioIdActiv = UbiInventarioIdActiv::fromNullableInt(null);
        $this->assertNull($ubiInventarioIdActiv);
    }

}
