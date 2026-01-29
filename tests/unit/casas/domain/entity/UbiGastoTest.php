<?php

namespace Tests\unit\casas\domain\entity;

use src\casas\domain\entity\UbiGasto;
use src\casas\domain\value_objects\UbiGastoCantidad;
use src\casas\domain\value_objects\UbiGastoTipo;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class UbiGastoTest extends myTest
{
    private UbiGasto $UbiGasto;

    public function setUp(): void
    {
        parent::setUp();
        $this->UbiGasto = new UbiGasto();
        $this->UbiGasto->setId_item(1);
        $this->UbiGasto->setId_ubi(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->UbiGasto->setId_item(1);
        $this->assertEquals(1, $this->UbiGasto->getId_item());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->UbiGasto->setId_ubi(1);
        $this->assertEquals(1, $this->UbiGasto->getId_ubi());
    }

    public function test_set_and_get_f_gasto()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->UbiGasto->setF_gasto($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->UbiGasto->getF_gasto());
        $this->assertEquals('2024-01-15 10:30:00', $this->UbiGasto->getF_gasto()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_tipo()
    {
        $tipoVo = new UbiGastoTipo(1);
        $this->UbiGasto->setTipoVo($tipoVo);
        $this->assertInstanceOf(UbiGastoTipo::class, $this->UbiGasto->getTipoVo());
        $this->assertEquals(1, $this->UbiGasto->getTipoVo()->value());
    }

    public function test_set_and_get_cantidad()
    {
        $cantidadVo = new UbiGastoCantidad(1);
        $this->UbiGasto->setCantidadVo($cantidadVo);
        $this->assertInstanceOf(UbiGastoCantidad::class, $this->UbiGasto->getCantidadVo());
        $this->assertEquals(1, $this->UbiGasto->getCantidadVo()->value());
    }

    public function test_set_all_attributes()
    {
        $ubiGasto = new UbiGasto();
        $attributes = [
            'id_item' => 1,
            'id_ubi' => 1,
            'f_gasto' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo' => new UbiGastoTipo(1),
            'cantidad' => new UbiGastoCantidad(1),
        ];
        $ubiGasto->setAllAttributes($attributes);

        $this->assertEquals(1, $ubiGasto->getId_item());
        $this->assertEquals(1, $ubiGasto->getId_ubi());
        $this->assertEquals('2024-01-15 10:30:00', $ubiGasto->getF_gasto()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $ubiGasto->getTipoVo()->value());
        $this->assertEquals(1, $ubiGasto->getCantidadVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $ubiGasto = new UbiGasto();
        $attributes = [
            'id_item' => 1,
            'id_ubi' => 1,
            'f_gasto' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo' => 1,
            'cantidad' => 1,
        ];
        $ubiGasto->setAllAttributes($attributes);

        $this->assertEquals(1, $ubiGasto->getId_item());
        $this->assertEquals(1, $ubiGasto->getId_ubi());
        $this->assertEquals('2024-01-15 10:30:00', $ubiGasto->getF_gasto()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $ubiGasto->getTipoVo()->value());
        $this->assertEquals(1, $ubiGasto->getCantidadVo()->value());
    }
}
