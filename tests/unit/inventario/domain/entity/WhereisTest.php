<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\Whereis;
use src\inventario\domain\value_objects\WhereisDocId;
use src\inventario\domain\value_objects\WhereisItemEgmId;
use src\inventario\domain\value_objects\WhereisItemId;
use Tests\myTest;

class WhereisTest extends myTest
{
    private Whereis $Whereis;

    public function setUp(): void
    {
        parent::setUp();
        $this->Whereis = new Whereis();
        $this->Whereis->setId_item_whereis(1);
    }

    public function test_set_and_get_id_item_whereis()
    {
        $this->Whereis->setId_item_whereis(1);
        $this->assertEquals(1, $this->Whereis->getId_item_whereis());
    }

    public function test_set_and_get_id_item_egm()
    {
        $id_item_egmVo = new WhereisItemEgmId(1);
        $this->Whereis->setIdItemEgmVo($id_item_egmVo);
        $this->assertInstanceOf(WhereisItemEgmId::class, $this->Whereis->getIdItemEgmVo());
        $this->assertEquals(1, $this->Whereis->getIdItemEgmVo()->value());
    }

    public function test_set_and_get_id_doc()
    {
        $id_docVo = new WhereisDocId(1);
        $this->Whereis->setIdDocVo($id_docVo);
        $this->assertInstanceOf(WhereisDocId::class, $this->Whereis->getIdDocVo());
        $this->assertEquals(1, $this->Whereis->getIdDocVo()->value());
    }

    public function test_set_all_attributes()
    {
        $whereis = new Whereis();
        $attributes = [
            'id_item_whereis' => 1,
            'id_item_egm' => new WhereisItemEgmId(1),
            'id_doc' => new WhereisDocId(1),
        ];
        $whereis->setAllAttributes($attributes);

        $this->assertEquals(1, $whereis->getId_item_whereis());
        $this->assertEquals(1, $whereis->getIdItemEgmVo()->value());
        $this->assertEquals(1, $whereis->getIdDocVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $whereis = new Whereis();
        $attributes = [
            'id_item_whereis' => 1,
            'id_item_egm' => 1,
            'id_doc' => 1,
        ];
        $whereis->setAllAttributes($attributes);

        $this->assertEquals(1, $whereis->getId_item_whereis());
        $this->assertEquals(1, $whereis->getIdItemEgmVo()->value());
        $this->assertEquals(1, $whereis->getIdDocVo()->value());
    }
}
