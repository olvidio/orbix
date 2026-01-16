<?php

namespace Tests\unit\cambios\domain\value_objects;

use src\cambios\domain\value_objects\AvisoTipoId;
use Tests\myTest;

class AvisoTipoIdTest extends myTest
{
    public function test_create_valid_avisoTipoId()
    {
        $avisoTipoId = new AvisoTipoId(AvisoTipoId::TIPO_LISTA);
        $this->assertEquals(1, $avisoTipoId->value());
    }

    public function test_invalid_avisoTipoId_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new AvisoTipoId(999);
    }

    public function test_equals_returns_true_for_same_avisoTipoId()
    {
        $avisoTipoId1 = new AvisoTipoId(AvisoTipoId::TIPO_LISTA);
        $avisoTipoId2 = new AvisoTipoId(AvisoTipoId::TIPO_LISTA);
        $this->assertTrue($avisoTipoId1->equals($avisoTipoId2));
    }

    public function test_equals_returns_false_for_different_avisoTipoId()
    {
        $avisoTipoId1 = new AvisoTipoId(AvisoTipoId::TIPO_LISTA);
        $avisoTipoId2 = new AvisoTipoId(AvisoTipoId::TIPO_MAIL);
        $this->assertFalse($avisoTipoId1->equals($avisoTipoId2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $avisoTipoId = AvisoTipoId::fromNullableInt(AvisoTipoId::TIPO_LISTA);
        $this->assertInstanceOf(AvisoTipoId::class, $avisoTipoId);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $avisoTipoId = AvisoTipoId::fromNullableInt(null);
        $this->assertNull($avisoTipoId);
    }

}
