<?php

namespace Tests\unit\misas\domain\entity;

use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaStatus;
use Tests\myTest;

class EncargoDiaTest extends myTest
{
    private EncargoDia $EncargoDia;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoDia = new EncargoDia();
        $this->EncargoDia->setUuidItemVo(new EncargoDiaId('550e8300-e29b-31d3-a716-336655330000'));
        $this->EncargoDia->setId_enc(1);
    }

    public function test_set_and_get_uuid_item()
    {
        $uuid_itemVo = new EncargoDiaId('550e8300-e29b-31d3-a716-336655330000');
        $this->EncargoDia->setUuidItemVo($uuid_itemVo);
        $this->assertInstanceOf(EncargoDiaId::class, $this->EncargoDia->getUuidItemVo());
        $this->assertEquals('550e8300-e29b-31d3-a716-336655330000', $this->EncargoDia->getUuidItemVo()->value());
    }

    public function test_set_and_get_id_enc()
    {
        $this->EncargoDia->setId_enc(1);
        $this->assertEquals(1, $this->EncargoDia->getId_enc());
    }

    public function test_set_and_get_id_nom()
    {
        $this->EncargoDia->setId_nom(1);
        $this->assertEquals(1, $this->EncargoDia->getId_nom());
    }

    public function test_set_and_get_sobserv()
    {
        $this->EncargoDia->setObserv('test');
        $this->assertEquals('test', $this->EncargoDia->getObserv());
    }

    public function test_set_and_get_status()
    {
        $statusVo = new EncargoDiaStatus(1);
        $this->EncargoDia->setStatusVo($statusVo);
        $this->assertInstanceOf(EncargoDiaStatus::class, $this->EncargoDia->getStatusVo());
        $this->assertEquals(1, $this->EncargoDia->getStatusVo()->value());
    }

    public function test_set_all_attributes()
    {
        $encargoDia = new EncargoDia();
        $attributes = [
            'uuid_item' => new EncargoDiaId('550e8300-e29b-31d3-a716-336655330000'),
            'id_enc' => 1,
            'id_nom' => 1,
            'observ' => 'test',
            'status' => new EncargoDiaStatus(1),
        ];
        $encargoDia->setAllAttributes($attributes);

        $this->assertEquals('550e8300-e29b-31d3-a716-336655330000', $encargoDia->getUuidItemVo()->value());
        $this->assertEquals(1, $encargoDia->getId_enc());
        $this->assertEquals(1, $encargoDia->getId_nom());
        $this->assertEquals('test', $encargoDia->getObserv());
        $this->assertEquals(1, $encargoDia->getStatusVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoDia = new EncargoDia();
        $attributes = [
            'uuid_item' => '550e8300-e29b-31d3-a716-336655330000',
            'id_enc' => 1,
            'id_nom' => 1,
            'observ' => 'test',
            'status' => 1,
        ];
        $encargoDia->setAllAttributes($attributes);

        $this->assertEquals('550e8300-e29b-31d3-a716-336655330000', $encargoDia->getUuidItemVo()->value());
        $this->assertEquals(1, $encargoDia->getId_enc());
        $this->assertEquals(1, $encargoDia->getId_nom());
        $this->assertEquals('test', $encargoDia->getObserv());
        $this->assertEquals(1, $encargoDia->getStatusVo()->value());
    }
}
