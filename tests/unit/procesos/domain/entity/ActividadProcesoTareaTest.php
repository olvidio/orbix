<?php

namespace Tests\unit\procesos\domain\entity;

use src\procesos\domain\entity\ActividadProcesoTarea;
use src\procesos\domain\value_objects\ActividadId;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\procesos\domain\value_objects\TareaId;
use src\procesos\domain\value_objects\TareaObserv;
use Tests\myTest;

class ActividadProcesoTareaTest extends myTest
{
    private ActividadProcesoTarea $ActividadProcesoTarea;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadProcesoTarea = new ActividadProcesoTarea();
        $this->ActividadProcesoTarea->setId_item(1);
        $this->ActividadProcesoTarea->setIdTipoProcesoVo(new ProcesoTipoId(1));
    }

    public function test_set_and_get_id_item()
    {
        $this->ActividadProcesoTarea->setId_item(1);
        $this->assertEquals(1, $this->ActividadProcesoTarea->getId_item());
    }

    public function test_set_and_get_id_tipo_proceso()
    {
        $id_tipo_procesoVo = new ProcesoTipoId(1);
        $this->ActividadProcesoTarea->setIdTipoProcesoVo($id_tipo_procesoVo);
        $this->assertInstanceOf(ProcesoTipoId::class, $this->ActividadProcesoTarea->getIdTipoProcesoVo());
        $this->assertEquals(1, $this->ActividadProcesoTarea->getIdTipoProcesoVo()->value());
    }

    public function test_set_and_get_id_activ()
    {
        $this->ActividadProcesoTarea->setId_activ(1);
        $this->assertEquals(1, $this->ActividadProcesoTarea->getId_activ());
    }

    public function test_set_and_get_completado()
    {
        $this->ActividadProcesoTarea->setCompletado(true);
        $this->assertTrue($this->ActividadProcesoTarea->isCompletado());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new TareaObserv('test');
        $this->ActividadProcesoTarea->setObservVo($observVo);
        $this->assertInstanceOf(TareaObserv::class, $this->ActividadProcesoTarea->getObservVo());
        $this->assertEquals('test', $this->ActividadProcesoTarea->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $actividadProcesoTarea = new ActividadProcesoTarea();
        $attributes = [
            'id_item' => 1,
            'id_tipo_proceso' => new ProcesoTipoId(1),
            'id_activ' => 1,
            'completado' => true,
            'observ' => new TareaObserv('test'),
        ];
        $actividadProcesoTarea->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadProcesoTarea->getId_item());
        $this->assertEquals(1, $actividadProcesoTarea->getIdTipoProcesoVo()->value());
        $this->assertEquals(1, $actividadProcesoTarea->getId_activ());
        $this->assertTrue($actividadProcesoTarea->isCompletado());
        $this->assertEquals('test', $actividadProcesoTarea->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadProcesoTarea = new ActividadProcesoTarea();
        $attributes = [
            'id_item' => 1,
            'id_tipo_proceso' => 1,
            'id_activ' => 1,
            'completado' => true,
            'observ' => 'test',
        ];
        $actividadProcesoTarea->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadProcesoTarea->getId_item());
        $this->assertEquals(1, $actividadProcesoTarea->getIdTipoProcesoVo()->value());
        $this->assertEquals(1, $actividadProcesoTarea->getId_activ());
        $this->assertTrue($actividadProcesoTarea->isCompletado());
        $this->assertEquals('test', $actividadProcesoTarea->getObservVo()->value());
    }
}
