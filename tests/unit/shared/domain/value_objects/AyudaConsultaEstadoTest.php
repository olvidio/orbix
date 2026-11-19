<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\value_objects;

use src\shared\domain\value_objects\AyudaConsultaEstado;
use Tests\myTest;

final class AyudaConsultaEstadoTest extends myTest
{
    public function test_create_valid(): void
    {
        $this->assertSame(2, (new AyudaConsultaEstado(AyudaConsultaEstado::RESUELTA))->value());
    }

    public function test_invalid_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new AyudaConsultaEstado(9);
    }

    public function test_to_string(): void
    {
        $this->assertSame('3', (string) new AyudaConsultaEstado(AyudaConsultaEstado::DESCARTADA));
    }

    public function test_fromNullableInt_null(): void
    {
        $this->assertNull(AyudaConsultaEstado::fromNullableInt(null));
        $this->assertSame(1, AyudaConsultaEstado::fromNullableInt(1)?->value());
    }

    public function test_los_estados_son_los_que_admite_la_tabla(): void
    {
        $this->assertSame([1, 2, 3], array_keys(AyudaConsultaEstado::getArrayEstado()));
    }

    public function test_descripcion_y_equals(): void
    {
        $pendiente = new AyudaConsultaEstado(AyudaConsultaEstado::PENDIENTE);
        $this->assertSame(AyudaConsultaEstado::getArrayEstado()[1], $pendiente->getDescripcion());
        $this->assertTrue($pendiente->equals(new AyudaConsultaEstado(1)));
        $this->assertFalse($pendiente->equals(new AyudaConsultaEstado(2)));
    }
}
