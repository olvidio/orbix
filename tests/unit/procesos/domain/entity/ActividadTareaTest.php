<?php

namespace Tests\unit\procesos\domain\entity;

use src\procesos\domain\entity\ActividadTarea;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\TareaId;
use Tests\myTest;

class ActividadTareaTest extends myTest
{
    private ActividadTarea $ActividadTarea;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadTarea = new ActividadTarea();
        $this->ActividadTarea->setIdFaseVo(new FaseId(1));
        $this->ActividadTarea->setIdTareaVo(new TareaId(1));
    }

    public function test_set_and_get_id_fase()
    {
        $id_faseVo = new FaseId(1);
        $this->ActividadTarea->setIdFaseVo($id_faseVo);
        $this->assertInstanceOf(FaseId::class, $this->ActividadTarea->getIdFaseVo());
        $this->assertEquals(1, $this->ActividadTarea->getIdFaseVo()->value());
    }

    public function test_set_and_get_id_tarea()
    {
        $id_tareaVo = new TareaId(1);
        $this->ActividadTarea->setIdTareaVo($id_tareaVo);
        $this->assertInstanceOf(TareaId::class, $this->ActividadTarea->getIdTareaVo());
        $this->assertEquals(1, $this->ActividadTarea->getIdTareaVo()->value());
    }

    public function test_set_and_get_desc_tarea()
    {
        $this->ActividadTarea->setDesc_tarea('test');
        $this->assertEquals('test', $this->ActividadTarea->getDesc_tarea());
    }

    public function test_set_all_attributes()
    {
        $actividadTarea = new ActividadTarea();
        $attributes = [
            'id_fase' => new FaseId(1),
            'id_tarea' => new TareaId(1),
            'desc_tarea' => 'test',
        ];
        $actividadTarea->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadTarea->getIdFaseVo()->value());
        $this->assertEquals(1, $actividadTarea->getIdTareaVo()->value());
        $this->assertEquals('test', $actividadTarea->getDesc_tarea());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadTarea = new ActividadTarea();
        $attributes = [
            'id_fase' => 1,
            'id_tarea' => 1,
            'desc_tarea' => 'test',
        ];
        $actividadTarea->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadTarea->getIdFaseVo()->value());
        $this->assertEquals(1, $actividadTarea->getIdTareaVo()->value());
        $this->assertEquals('test', $actividadTarea->getDesc_tarea());
    }
}
