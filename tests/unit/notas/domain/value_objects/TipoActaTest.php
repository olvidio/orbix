<?php

namespace Tests\unit\notas\domain\value_objects;

use src\notas\domain\value_objects\TipoActa;
use Tests\myTest;

class TipoActaTest extends myTest
{
    public function test_create_valid_tipoActa()
    {
        $tipoActa = new TipoActa(TipoActa::FORMATO_ACTA);
        $this->assertEquals(1, $tipoActa->value());
    }

    public function test_to_string_returns_tipoActa_value()
    {
        $tipoActa = new TipoActa(TipoActa::FORMATO_ACTA);
        $this->assertEquals(TipoActa::FORMATO_ACTA, (string)$tipoActa);
    }

    public function test_fromNullableInt_returns_instance_for_valid_value()
    {
        $tipoActa = TipoActa::fromNullableInt(TipoActa::FORMATO_ACTA);
        $this->assertInstanceOf(TipoActa::class, $tipoActa);
    }

    public function test_fromNullableInt_returns_null_for_null_value()
    {
        $tipoActa = TipoActa::fromNullableInt(null);
        $this->assertNull($tipoActa);
    }

}
