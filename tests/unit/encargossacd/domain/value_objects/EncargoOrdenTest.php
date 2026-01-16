<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoOrden;
use Tests\myTest;

class EncargoOrdenTest extends myTest
{
    public function test_create_valid_encargoOrden()
    {
        $encargoOrden = new EncargoOrden(123);
        $this->assertEquals(123, $encargoOrden->value());
    }

    public function test_equals_returns_true_for_same_encargoOrden()
    {
        $encargoOrden1 = new EncargoOrden(123);
        $encargoOrden2 = new EncargoOrden(123);
        $this->assertTrue($encargoOrden1->equals($encargoOrden2));
    }

    public function test_equals_returns_false_for_different_encargoOrden()
    {
        $encargoOrden1 = new EncargoOrden(123);
        $encargoOrden2 = new EncargoOrden(456);
        $this->assertFalse($encargoOrden1->equals($encargoOrden2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $encargoOrden = EncargoOrden::fromNullableInt(123);
        $this->assertInstanceOf(EncargoOrden::class, $encargoOrden);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $encargoOrden = EncargoOrden::fromNullableInt(null);
        $this->assertNull($encargoOrden);
    }

}
