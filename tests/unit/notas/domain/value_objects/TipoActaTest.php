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

    public function test_fromNullableInt_normaliza_cero_a_formato_acta(): void
    {
        $tipoActa = TipoActa::fromNullableInt(0);
        $this->assertInstanceOf(TipoActa::class, $tipoActa);
        $this->assertSame(TipoActa::FORMATO_ACTA, $tipoActa->value());
    }

    public function test_fromPg_trata_vacio_como_formato_acta(): void
    {
        $this->assertSame(TipoActa::FORMATO_ACTA, TipoActa::fromPg(null)->value());
        $this->assertSame(TipoActa::FORMATO_ACTA, TipoActa::fromPg(0)->value());
        $this->assertSame(TipoActa::FORMATO_ACTA, TipoActa::fromPg('')->value());
        $this->assertSame(TipoActa::FORMATO_ACTA, TipoActa::fromPg(false)->value());
        $this->assertSame(TipoActa::FORMATO_CERTIFICADO, TipoActa::fromPg(2)->value());
        $this->assertSame(TipoActa::FORMATO_CERTIFICADO, TipoActa::fromPg('2')->value());
    }

    public function test_constructor_rechaza_cero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TipoActa(0);
    }
}
