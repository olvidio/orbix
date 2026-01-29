<?php

namespace Tests\unit\actividades\domain\entity;

use src\actividades\domain\entity\TipoDeActividad;
use src\actividades\domain\value_objects\TipoActivNombre;
use Tests\myTest;

class TipoDeActividadTest extends myTest
{
    private TipoDeActividad $TipoDeActividad;

    public function setUp(): void
    {
        parent::setUp();
        $this->TipoDeActividad = new TipoDeActividad();
        $this->TipoDeActividad->setId_tipo_activ(1);
        $this->TipoDeActividad->setNombreVo(new TipoActivNombre('Test Name'));
    }

    public function test_set_and_get_id_tipo_activ()
    {
        $this->TipoDeActividad->setId_tipo_activ(1);
        $this->assertEquals(1, $this->TipoDeActividad->getId_tipo_activ());
    }

    public function test_set_and_get_nombre()
    {
        $nombreVo = new TipoActivNombre('Test Name');
        $this->TipoDeActividad->setNombreVo($nombreVo);
        $this->assertInstanceOf(TipoActivNombre::class, $this->TipoDeActividad->getNombreVo());
        $this->assertEquals('Test Name', $this->TipoDeActividad->getNombreVo()->value());
    }

    public function test_set_and_get_id_tipo_proceso_sv()
    {
        $this->TipoDeActividad->setId_tipo_proceso_sv(1);
        $this->assertEquals(1, $this->TipoDeActividad->getId_tipo_proceso_sv());
    }

    public function test_set_and_get_id_tipo_proceso_ex_sv()
    {
        $this->TipoDeActividad->setId_tipo_proceso_ex_sv(1);
        $this->assertEquals(1, $this->TipoDeActividad->getId_tipo_proceso_ex_sv());
    }

    public function test_set_and_get_id_tipo_proceso_sf()
    {
        $this->TipoDeActividad->setId_tipo_proceso_sf(1);
        $this->assertEquals(1, $this->TipoDeActividad->getId_tipo_proceso_sf());
    }

    public function test_set_and_get_id_tipo_proceso_ex_sf()
    {
        $this->TipoDeActividad->setId_tipo_proceso_ex_sf(1);
        $this->assertEquals(1, $this->TipoDeActividad->getId_tipo_proceso_ex_sf());
    }

    public function test_set_all_attributes()
    {
        $tipoDeActividad = new TipoDeActividad();
        $attributes = [
            'id_tipo_activ' => 1,
            'nombre' => new TipoActivNombre('Test Name'),
            'id_tipo_proceso_sv' => 1,
            'id_tipo_proceso_ex_sv' => 1,
            'id_tipo_proceso_sf' => 1,
            'id_tipo_proceso_ex_sf' => 1,
        ];
        $tipoDeActividad->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoDeActividad->getId_tipo_activ());
        $this->assertEquals('Test Name', $tipoDeActividad->getNombreVo()->value());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_sv());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_ex_sv());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_sf());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_ex_sf());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tipoDeActividad = new TipoDeActividad();
        $attributes = [
            'id_tipo_activ' => 1,
            'nombre' => 'Test Name',
            'id_tipo_proceso_sv' => 1,
            'id_tipo_proceso_ex_sv' => 1,
            'id_tipo_proceso_sf' => 1,
            'id_tipo_proceso_ex_sf' => 1,
        ];
        $tipoDeActividad->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoDeActividad->getId_tipo_activ());
        $this->assertEquals('Test Name', $tipoDeActividad->getNombreVo()->value());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_sv());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_ex_sv());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_sf());
        $this->assertEquals(1, $tipoDeActividad->getId_tipo_proceso_ex_sf());
    }
}
