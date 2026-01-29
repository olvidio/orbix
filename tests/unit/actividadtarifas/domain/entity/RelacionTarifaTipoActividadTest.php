<?php

namespace Tests\unit\actividadtarifas\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use Tests\myTest;

class RelacionTarifaTipoActividadTest extends myTest
{
    private RelacionTarifaTipoActividad $RelacionTarifaTipoActividad;

    public function setUp(): void
    {
        parent::setUp();
        $this->RelacionTarifaTipoActividad = new RelacionTarifaTipoActividad();
        $this->RelacionTarifaTipoActividad->setId_item(1);
        $this->RelacionTarifaTipoActividad->setIdTarifaVo(new TarifaId(1));
    }

    public function test_set_and_get_id_item()
    {
        $this->RelacionTarifaTipoActividad->setId_item(1);
        $this->assertEquals(1, $this->RelacionTarifaTipoActividad->getId_item());
    }

    public function test_set_and_get_id_tarifa()
    {
        $id_tarifaVo = new TarifaId(1);
        $this->RelacionTarifaTipoActividad->setIdTarifaVo($id_tarifaVo);
        $this->assertInstanceOf(TarifaId::class, $this->RelacionTarifaTipoActividad->getIdTarifaVo());
        $this->assertEquals(1, $this->RelacionTarifaTipoActividad->getIdTarifaVo()->value());
    }

    public function test_set_and_get_id_tipo_activ()
    {
        $this->RelacionTarifaTipoActividad->setId_tipo_activ(123456);
        $this->assertEquals(123456, $this->RelacionTarifaTipoActividad->getId_tipo_activ());
    }

    public function test_set_and_get_id_serie()
    {
        $id_serieVo = new SerieId(1);
        $this->RelacionTarifaTipoActividad->setIdSerieVo($id_serieVo);
        $this->assertInstanceOf(SerieId::class, $this->RelacionTarifaTipoActividad->getIdSerieVo());
        $this->assertEquals(1, $this->RelacionTarifaTipoActividad->getIdSerieVo()->value());
    }

    public function test_set_all_attributes()
    {
        $relacionTarifaTipoActividad = new RelacionTarifaTipoActividad();
        $attributes = [
            'id_item' => 1,
            'id_tarifa' => new TarifaId(1),
            'id_tipo_activ' => 123456,
            'id_serie' => new SerieId(1),
        ];
        $relacionTarifaTipoActividad->setAllAttributes($attributes);

        $this->assertEquals(1, $relacionTarifaTipoActividad->getId_item());
        $this->assertEquals(1, $relacionTarifaTipoActividad->getIdTarifaVo()->value());
        $this->assertEquals(123456, $relacionTarifaTipoActividad->getId_tipo_activ());
        $this->assertEquals(1, $relacionTarifaTipoActividad->getIdSerieVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $relacionTarifaTipoActividad = new RelacionTarifaTipoActividad();
        $attributes = [
            'id_item' => 1,
            'id_tarifa' => 1,
            'id_tipo_activ' => 123456,
            'id_serie' => 1,
        ];
        $relacionTarifaTipoActividad->setAllAttributes($attributes);

        $this->assertEquals(1, $relacionTarifaTipoActividad->getId_item());
        $this->assertEquals(1, $relacionTarifaTipoActividad->getIdTarifaVo()->value());
        $this->assertEquals(123456, $relacionTarifaTipoActividad->getId_tipo_activ());
        $this->assertEquals(1, $relacionTarifaTipoActividad->getIdSerieVo()->value());
    }
}
