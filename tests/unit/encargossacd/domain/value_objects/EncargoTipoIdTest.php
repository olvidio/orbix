<?php

namespace Tests\unit\encargossacd\domain\value_objects;

use src\encargossacd\domain\value_objects\EncargoTipoId;
use Tests\myTest;

class EncargoTipoIdTest extends myTest
{
    public function test_create_valid_encargoTipoId()
    {
        $encargoTipoId = new EncargoTipoId(123);
        $this->assertEquals(123, $encargoTipoId->value());
    }

    public function test_equals_returns_true_for_same_encargoTipoId()
    {
        $encargoTipoId1 = new EncargoTipoId(123);
        $encargoTipoId2 = new EncargoTipoId(123);
        $this->assertTrue($encargoTipoId1->equals($encargoTipoId2));
    }

    public function test_equals_returns_false_for_different_encargoTipoId()
    {
        $encargoTipoId1 = new EncargoTipoId(123);
        $encargoTipoId2 = new EncargoTipoId(456);
        $this->assertFalse($encargoTipoId1->equals($encargoTipoId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $encargoTipoId = EncargoTipoId::fromNullableInt(123);
        $this->assertInstanceOf(EncargoTipoId::class, $encargoTipoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $encargoTipoId = EncargoTipoId::fromNullableInt(null);
        $this->assertNull($encargoTipoId);
    }

}
