<?php

namespace Tests\unit\inventario\domain\value_objects;

use src\inventario\domain\value_objects\EgmGrupoId;
use Tests\myTest;

class EgmGrupoIdTest extends myTest
{
    public function test_create_valid_egmGrupoId()
    {
        $egmGrupoId = new EgmGrupoId(123);
        $this->assertEquals(123, $egmGrupoId->value());
    }

    public function test_equals_returns_true_for_same_egmGrupoId()
    {
        $egmGrupoId1 = new EgmGrupoId(123);
        $egmGrupoId2 = new EgmGrupoId(123);
        $this->assertTrue($egmGrupoId1->equals($egmGrupoId2));
    }

    public function test_equals_returns_false_for_different_egmGrupoId()
    {
        $egmGrupoId1 = new EgmGrupoId(123);
        $egmGrupoId2 = new EgmGrupoId(456);
        $this->assertFalse($egmGrupoId1->equals($egmGrupoId2));
    }

    public function test_to_string_returns_egmGrupoId_value()
    {
        $egmGrupoId = new EgmGrupoId(123);
        $this->assertEquals(123, (string)$egmGrupoId);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $egmGrupoId = EgmGrupoId::fromNullableInt(123);
        $this->assertInstanceOf(EgmGrupoId::class, $egmGrupoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $egmGrupoId = EgmGrupoId::fromNullableInt(null);
        $this->assertNull($egmGrupoId);
    }

}
