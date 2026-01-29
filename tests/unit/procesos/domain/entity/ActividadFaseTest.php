<?php

namespace Tests\unit\procesos\domain\entity;

use src\procesos\domain\entity\ActividadFase;
use src\procesos\domain\value_objects\FaseId;
use Tests\myTest;

class ActividadFaseTest extends myTest
{
    private ActividadFase $ActividadFase;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadFase = new ActividadFase();
        $this->ActividadFase->setIdFaseVo(new FaseId(1));
    }

    public function test_set_and_get_id_fase()
    {
        $id_faseVo = new FaseId(1);
        $this->ActividadFase->setIdFaseVo($id_faseVo);
        $this->assertInstanceOf(FaseId::class, $this->ActividadFase->getIdFaseVo());
        $this->assertEquals(1, $this->ActividadFase->getIdFaseVo()->value());
    }

    public function test_set_and_get_desc_fase()
    {
        $this->ActividadFase->setDesc_fase('test');
        $this->assertEquals('test', $this->ActividadFase->getDesc_fase());
    }

    public function test_set_and_get_sf()
    {
        $this->ActividadFase->setSf(true);
        $this->assertTrue($this->ActividadFase->isSf());
    }

    public function test_set_and_get_sv()
    {
        $this->ActividadFase->setSv(true);
        $this->assertTrue($this->ActividadFase->isSv());
    }

    public function test_set_all_attributes()
    {
        $actividadFase = new ActividadFase();
        $attributes = [
            'id_fase' => new FaseId(1),
            'desc_fase' => 'test',
            'sf' => true,
            'sv' => true,
        ];
        $actividadFase->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadFase->getIdFaseVo()->value());
        $this->assertEquals('test', $actividadFase->getDesc_fase());
        $this->assertTrue($actividadFase->isSf());
        $this->assertTrue($actividadFase->isSv());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadFase = new ActividadFase();
        $attributes = [
            'id_fase' => 1,
            'desc_fase' => 'test',
            'sf' => true,
            'sv' => true,
        ];
        $actividadFase->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadFase->getIdFaseVo()->value());
        $this->assertEquals('test', $actividadFase->getDesc_fase());
        $this->assertTrue($actividadFase->isSf());
        $this->assertTrue($actividadFase->isSv());
    }
}
