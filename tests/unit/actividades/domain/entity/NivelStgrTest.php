<?php

namespace Tests\unit\actividades\domain\entity;

use src\actividades\domain\entity\NivelStgr;
use src\actividades\domain\value_objects\NivelStgrBreve;
use src\actividades\domain\value_objects\NivelStgrDesc;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\NivelStgrOrden;
use Tests\myTest;

class NivelStgrTest extends myTest
{
    private NivelStgr $NivelStgr;

    public function setUp(): void
    {
        parent::setUp();
        $this->NivelStgr = new NivelStgr();
        $this->NivelStgr->setNivelStgrVo(new NivelStgrId(1));
        $this->NivelStgr->setDescNivelVo(new NivelStgrDesc('Test value'));
    }

    public function test_set_and_get_nivel_stgr()
    {
        $nivel_stgrVo = new NivelStgrId(1);
        $this->NivelStgr->setNivelStgrVo($nivel_stgrVo);
        $this->assertInstanceOf(NivelStgrId::class, $this->NivelStgr->getNivelStgrVo());
        $this->assertEquals(1, $this->NivelStgr->getNivelStgrVo()->value());
    }

    public function test_set_and_get_desc_nivel()
    {
        $desc_nivelVo = new NivelStgrDesc('Test value');
        $this->NivelStgr->setDescNivelVo($desc_nivelVo);
        $this->assertInstanceOf(NivelStgrDesc::class, $this->NivelStgr->getDescNivelVo());
        $this->assertEquals('Test value', $this->NivelStgr->getDescNivelVo()->value());
    }

    public function test_set_and_get_desc_breve()
    {
        $desc_breveVo = new NivelStgrBreve('ST');
        $this->NivelStgr->setDescBreveVo($desc_breveVo);
        $this->assertInstanceOf(NivelStgrBreve::class, $this->NivelStgr->getDescBreveVo());
        $this->assertEquals('ST', $this->NivelStgr->getDescBreveVo()->value());
    }

    public function test_set_and_get_orden()
    {
        $ordenVo = new NivelStgrOrden(1);
        $this->NivelStgr->setOrdenVo($ordenVo);
        $this->assertInstanceOf(NivelStgrOrden::class, $this->NivelStgr->getOrdenVo());
        $this->assertEquals(1, $this->NivelStgr->getOrdenVo()->value());
    }

    public function test_set_all_attributes()
    {
        $nivelStgr = new NivelStgr();
        $attributes = [
            'nivel_stgr' => new NivelStgrId(1),
            'desc_nivel' => new NivelStgrDesc('Test value'),
            'desc_breve' => new NivelStgrBreve('ST'),
            'orden' => new NivelStgrOrden(1),
        ];
        $nivelStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $nivelStgr->getNivelStgrVo()->value());
        $this->assertEquals('Test value', $nivelStgr->getDescNivelVo()->value());
        $this->assertEquals('ST', $nivelStgr->getDescBreveVo()->value());
        $this->assertEquals(1, $nivelStgr->getOrdenVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $nivelStgr = new NivelStgr();
        $attributes = [
            'nivel_stgr' => 1,
            'desc_nivel' => 'Test value',
            'desc_breve' => 'ST',
            'orden' => 1,
        ];
        $nivelStgr->setAllAttributes($attributes);

        $this->assertEquals(1, $nivelStgr->getNivelStgrVo()->value());
        $this->assertEquals('Test value', $nivelStgr->getDescNivelVo()->value());
        $this->assertEquals('ST', $nivelStgr->getDescBreveVo()->value());
        $this->assertEquals(1, $nivelStgr->getOrdenVo()->value());
    }
}
