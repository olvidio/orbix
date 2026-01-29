<?php

namespace Tests\unit\personas\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\personas\domain\entity\UltimaAsistencia;
use src\personas\domain\value_objects\AsistenciaDescripcionText;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class UltimaAsistenciaTest extends myTest
{
    private UltimaAsistencia $UltimaAsistencia;

    public function setUp(): void
    {
        parent::setUp();
        $this->UltimaAsistencia = new UltimaAsistencia();
        $this->UltimaAsistencia->setId_item(1);
        $this->UltimaAsistencia->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->UltimaAsistencia->setId_item(1);
        $this->assertEquals(1, $this->UltimaAsistencia->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->UltimaAsistencia->setId_nom(1);
        $this->assertEquals(1, $this->UltimaAsistencia->getId_nom());
    }

    public function test_set_and_get_id_tipo_activ()
    {
        $id_tipo_activVo = new ActividadTipoId(123456);
        $this->UltimaAsistencia->setIdTipoActivVo($id_tipo_activVo);
        $this->assertInstanceOf(ActividadTipoId::class, $this->UltimaAsistencia->getIdTipoActivVo());
        $this->assertEquals(123456, $this->UltimaAsistencia->getIdTipoActivVo()->value());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->UltimaAsistencia->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->UltimaAsistencia->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->UltimaAsistencia->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_descripcion()
    {
        $descripcionVo = new AsistenciaDescripcionText('Test');
        $this->UltimaAsistencia->setDescripcionVo($descripcionVo);
        $this->assertInstanceOf(AsistenciaDescripcionText::class, $this->UltimaAsistencia->getDescripcionVo());
        $this->assertEquals('Test', $this->UltimaAsistencia->getDescripcionVo()->value());
    }

    public function test_set_and_get_cdr()
    {
        $this->UltimaAsistencia->setCdr(true);
        $this->assertTrue($this->UltimaAsistencia->isCdr());
    }

    public function test_set_all_attributes()
    {
        $ultimaAsistencia = new UltimaAsistencia();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_tipo_activ' => new ActividadTipoId(123456),
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'descripcion' => new AsistenciaDescripcionText('Test'),
            'cdr' => true,
        ];
        $ultimaAsistencia->setAllAttributes($attributes);

        $this->assertEquals(1, $ultimaAsistencia->getId_item());
        $this->assertEquals(1, $ultimaAsistencia->getId_nom());
        $this->assertEquals(123456, $ultimaAsistencia->getIdTipoActivVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $ultimaAsistencia->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test', $ultimaAsistencia->getDescripcionVo()->value());
        $this->assertTrue($ultimaAsistencia->isCdr());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $ultimaAsistencia = new UltimaAsistencia();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_tipo_activ' => 123456,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'descripcion' => 'Test',
            'cdr' => true,
        ];
        $ultimaAsistencia->setAllAttributes($attributes);

        $this->assertEquals(1, $ultimaAsistencia->getId_item());
        $this->assertEquals(1, $ultimaAsistencia->getId_nom());
        $this->assertEquals(123456, $ultimaAsistencia->getIdTipoActivVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $ultimaAsistencia->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test', $ultimaAsistencia->getDescripcionVo()->value());
        $this->assertTrue($ultimaAsistencia->isCdr());
    }
}
