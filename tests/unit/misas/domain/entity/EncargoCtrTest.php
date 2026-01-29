<?php

namespace Tests\unit\misas\domain\entity;

use src\misas\domain\entity\EncargoCtr;
use src\misas\domain\value_objects\EncargoCtrId;
use Tests\myTest;

class EncargoCtrTest extends myTest
{
    private EncargoCtr $EncargoCtr;

    public function setUp(): void
    {
        parent::setUp();
        $this->EncargoCtr = new EncargoCtr();
        $this->EncargoCtr->setUuidItemVo(new EncargoCtrId('550e8400-e29b-41d4-a716-446655440000'));
        $this->EncargoCtr->setId_enc(1);
    }

    public function test_set_and_get_uuid_item()
    {
        $uuid_itemVo = new EncargoCtrId('550e8400-e29b-41d4-a716-446655440000');
        $this->EncargoCtr->setUuidItemVo($uuid_itemVo);
        $this->assertInstanceOf(EncargoCtrId::class, $this->EncargoCtr->getUuidItemVo());
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $this->EncargoCtr->getUuidItemVo()->value());
    }

    public function test_set_and_get_id_enc()
    {
        $this->EncargoCtr->setId_enc(1);
        $this->assertEquals(1, $this->EncargoCtr->getId_enc());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->EncargoCtr->setId_ubi(1);
        $this->assertEquals(1, $this->EncargoCtr->getId_ubi());
    }

    public function test_set_all_attributes()
    {
        $encargoCtr = new EncargoCtr();
        $attributes = [
            'uuid_item' => new EncargoCtrId('550e8400-e29b-41d4-a716-446655440000'),
            'id_enc' => 1,
            'id_ubi' => 1,
        ];
        $encargoCtr->setAllAttributes($attributes);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $encargoCtr->getUuidItemVo()->value());
        $this->assertEquals(1, $encargoCtr->getId_enc());
        $this->assertEquals(1, $encargoCtr->getId_ubi());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $encargoCtr = new EncargoCtr();
        $attributes = [
            'uuid_item' => '550e8400-e29b-41d4-a716-446655440000',
            'id_enc' => 1,
            'id_ubi' => 1,
        ];
        $encargoCtr->setAllAttributes($attributes);

        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $encargoCtr->getUuidItemVo()->value());
        $this->assertEquals(1, $encargoCtr->getId_enc());
        $this->assertEquals(1, $encargoCtr->getId_ubi());
    }
}
