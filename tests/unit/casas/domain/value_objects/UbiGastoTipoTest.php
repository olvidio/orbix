<?php

namespace Tests\unit\casas\domain\value_objects;

use src\casas\domain\value_objects\UbiGastoTipo;
use Tests\myTest;

class UbiGastoTipoTest extends myTest
{
    public function test_create_valid_ubiGastoTipo()
    {
        $ubiGastoTipo = new UbiGastoTipo(UbiGastoTipo::APORTACION_SV);
        $this->assertEquals(1, $ubiGastoTipo->value());
    }

    public function test_invalid_ubiGastoTipo_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new UbiGastoTipo(999);
    }

    public function test_equals_returns_true_for_same_ubiGastoTipo()
    {
        $ubiGastoTipo1 = new UbiGastoTipo(UbiGastoTipo::APORTACION_SV);
        $ubiGastoTipo2 = new UbiGastoTipo(UbiGastoTipo::APORTACION_SV);
        $this->assertTrue($ubiGastoTipo1->equals($ubiGastoTipo2));
    }

    public function test_equals_returns_false_for_different_ubiGastoTipo()
    {
        $ubiGastoTipo1 = new UbiGastoTipo(UbiGastoTipo::APORTACION_SV);
        $ubiGastoTipo2 = new UbiGastoTipo(UbiGastoTipo::APORTACION_SF);
        $this->assertFalse($ubiGastoTipo1->equals($ubiGastoTipo2));
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $ubiGastoTipo = UbiGastoTipo::fromNullableInt(UbiGastoTipo::APORTACION_SV);
        $this->assertInstanceOf(UbiGastoTipo::class, $ubiGastoTipo);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $ubiGastoTipo = UbiGastoTipo::fromNullableInt(null);
        $this->assertNull($ubiGastoTipo);
    }

}
