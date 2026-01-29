<?php

namespace Tests\unit\ubis\domain\entity;

use src\ubis\domain\entity\DescTeleco;
use src\ubis\domain\value_objects\DescTelecoOrder;
use src\ubis\domain\value_objects\int;
use src\ubis\domain\value_objects\int;
use Tests\myTest;

class DescTelecoTest extends myTest
{
    private DescTeleco $DescTeleco;

    public function setUp(): void
    {
        parent::setUp();
        $this->DescTeleco = new DescTeleco();
        $this->DescTeleco->setId_item(1);
        $this->DescTeleco->setId_tipo_teleco(new int('TST'));
    }

    public function test_set_and_get_id_item()
    {
        $this->DescTeleco->setId_item(1);
        $this->assertEquals(1, $this->DescTeleco->getId_item());
    }

    public function test_set_and_get_orden()
    {
        $ordenVo = new DescTelecoOrder(1);
        $this->DescTeleco->setOrdenVo($ordenVo);
        $this->assertInstanceOf(DescTelecoOrder::class, $this->DescTeleco->getOrdenVo());
        $this->assertEquals(1, $this->DescTeleco->getOrdenVo()->value());
    }

    public function test_set_and_get_id_tipo_teleco()
    {
        $id_tipo_telecoVo = new int('TST');
        $this->DescTeleco->setId_tipo_teleco($id_tipo_telecoVo);
        $this->assertInstanceOf(int::class, $this->DescTeleco->getId_tipo_teleco());
        $this->assertEquals('TST', $this->DescTeleco->getId_tipo_teleco()->value());
    }

    public function test_set_and_get_desc_teleco()
    {
        $desc_telecoVo = new int('Test');
        $this->DescTeleco->setDescTelecoVo($desc_telecoVo);
        $this->assertInstanceOf(int::class, $this->DescTeleco->getDescTelecoVo());
        $this->assertEquals('Test', $this->DescTeleco->getDescTelecoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $descTeleco = new DescTeleco();
        $attributes = [
            'id_item' => 1,
            'orden' => new DescTelecoOrder(1),
            'id_tipo_teleco' => new int('TST'),
            'desc_teleco' => new int('Test'),
        ];
        $descTeleco->setAllAttributes($attributes);

        $this->assertEquals(1, $descTeleco->getId_item());
        $this->assertEquals(1, $descTeleco->getOrdenVo()->value());
        $this->assertEquals('TST', $descTeleco->getId_tipo_teleco()->value());
        $this->assertEquals('Test', $descTeleco->getDescTelecoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $descTeleco = new DescTeleco();
        $attributes = [
            'id_item' => 1,
            'orden' => 1,
            'id_tipo_teleco' => 'TST',
            'desc_teleco' => 'Test',
        ];
        $descTeleco->setAllAttributes($attributes);

        $this->assertEquals(1, $descTeleco->getId_item());
        $this->assertEquals(1, $descTeleco->getOrdenVo()->value());
        $this->assertEquals('TST', $descTeleco->getId_tipo_teleco()->value());
        $this->assertEquals('Test', $descTeleco->getDescTelecoVo()->value());
    }
}
