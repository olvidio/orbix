<?php

namespace Tests\unit\procesos\domain\entity;

use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\entity\TareaProceso;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\procesos\domain\value_objects\TareaId;
use Tests\myTest;

class TareaProcesoTest extends myTest
{
    private TareaProceso $TareaProceso;

    public function setUp(): void
    {
        parent::setUp();
        $this->TareaProceso = new TareaProceso();
        $this->TareaProceso->setId_item(1);
        $this->TareaProceso->setIdTipoProcesoVo(new ProcesoTipoId(1));
    }

    public function test_set_and_get_id_item()
    {
        $this->TareaProceso->setId_item(1);
        $this->assertEquals(1, $this->TareaProceso->getId_item());
    }

    public function test_set_and_get_id_tipo_proceso()
    {
        $id_tipo_procesoVo = new ProcesoTipoId(1);
        $this->TareaProceso->setIdTipoProcesoVo($id_tipo_procesoVo);
        $this->assertInstanceOf(ProcesoTipoId::class, $this->TareaProceso->getIdTipoProcesoVo());
        $this->assertEquals(1, $this->TareaProceso->getIdTipoProcesoVo()->value());
    }

    public function test_set_and_get_id_fase()
    {
        $id_faseVo = new FaseId(1);
        $this->TareaProceso->setIdFaseVo($id_faseVo);
        $this->assertInstanceOf(FaseId::class, $this->TareaProceso->getIdFaseVo());
        $this->assertEquals(1, $this->TareaProceso->getIdFaseVo()->value());
    }

    public function test_set_and_get_id_tarea()
    {
        $id_tareaVo = new TareaId(1);
        $this->TareaProceso->setIdTareaVo($id_tareaVo);
        $this->assertInstanceOf(TareaId::class, $this->TareaProceso->getIdTareaVo());
        $this->assertEquals(1, $this->TareaProceso->getIdTareaVo()->value());
    }

    public function test_set_and_get_status()
    {
        $statusVo = new StatusId(1);
        $this->TareaProceso->setStatusVo($statusVo);
        $this->assertInstanceOf(StatusId::class, $this->TareaProceso->getStatusVo());
        $this->assertEquals(1, $this->TareaProceso->getStatusVo()->value());
    }

    public function test_set_and_get_id_of_responsable()
    {
        $this->TareaProceso->setId_of_responsable(1);
        $this->assertEquals(1, $this->TareaProceso->getId_of_responsable());
    }

    public function test_set_all_attributes()
    {
        $tareaProceso = new TareaProceso();
        $attributes = [
            'id_item' => 1,
            'id_tipo_proceso' => new ProcesoTipoId(1),
            'id_fase' => new FaseId(1),
            'id_tarea' => new TareaId(1),
            'status' => new StatusId(1),
            'id_of_responsable' => 1,
        ];
        $tareaProceso->setAllAttributes($attributes);

        $this->assertEquals(1, $tareaProceso->getId_item());
        $this->assertEquals(1, $tareaProceso->getIdTipoProcesoVo()->value());
        $this->assertEquals(1, $tareaProceso->getIdFaseVo()->value());
        $this->assertEquals(1, $tareaProceso->getIdTareaVo()->value());
        $this->assertEquals(1, $tareaProceso->getStatusVo()->value());
        $this->assertEquals(1, $tareaProceso->getId_of_responsable());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tareaProceso = new TareaProceso();
        $attributes = [
            'id_item' => 1,
            'id_tipo_proceso' => 1,
            'id_fase' => 1,
            'id_tarea' => 1,
            'status' => 1,
            'id_of_responsable' => 1,
        ];
        $tareaProceso->setAllAttributes($attributes);

        $this->assertEquals(1, $tareaProceso->getId_item());
        $this->assertEquals(1, $tareaProceso->getIdTipoProcesoVo()->value());
        $this->assertEquals(1, $tareaProceso->getIdFaseVo()->value());
        $this->assertEquals(1, $tareaProceso->getIdTareaVo()->value());
        $this->assertEquals(1, $tareaProceso->getStatusVo()->value());
        $this->assertEquals(1, $tareaProceso->getId_of_responsable());
    }
}
