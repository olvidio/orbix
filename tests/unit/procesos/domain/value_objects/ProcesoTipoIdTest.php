<?php

namespace Tests\unit\procesos\domain\value_objects;

use src\procesos\domain\value_objects\ProcesoTipoId;
use Tests\myTest;

class ProcesoTipoIdTest extends myTest
{
    public function test_create_valid_procesoTipoId()
    {
        $procesoTipoId = new ProcesoTipoId(123);
        $this->assertEquals(123, $procesoTipoId->value());
    }

    public function test_equals_returns_true_for_same_procesoTipoId()
    {
        $procesoTipoId1 = new ProcesoTipoId(123);
        $procesoTipoId2 = new ProcesoTipoId(123);
        $this->assertTrue($procesoTipoId1->equals($procesoTipoId2));
    }

    public function test_equals_returns_false_for_different_procesoTipoId()
    {
        $procesoTipoId1 = new ProcesoTipoId(123);
        $procesoTipoId2 = new ProcesoTipoId(456);
        $this->assertFalse($procesoTipoId1->equals($procesoTipoId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $procesoTipoId = ProcesoTipoId::fromNullableInt(123);
        $this->assertInstanceOf(ProcesoTipoId::class, $procesoTipoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $procesoTipoId = ProcesoTipoId::fromNullableInt(null);
        $this->assertNull($procesoTipoId);
    }

}
