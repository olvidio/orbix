<?php

namespace Tests\unit\actividadestudios\domain\entity;

use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\value_objects\AvisProfesor;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ActividadAsignaturaTest extends myTest
{
    private ActividadAsignatura $ActividadAsignatura;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadAsignatura = new ActividadAsignatura();
        $this->ActividadAsignatura->setId_schema(1);
        $this->ActividadAsignatura->setId_activ(1);
    }

    public function test_set_and_get_id_schema()
    {
        $this->ActividadAsignatura->setId_schema(1);
        $this->assertEquals(1, $this->ActividadAsignatura->getId_schema());
    }

    public function test_set_and_get_id_activ()
    {
        $this->ActividadAsignatura->setId_activ(1);
        $this->assertEquals(1, $this->ActividadAsignatura->getId_activ());
    }

    public function test_set_and_get_id_asignatura()
    {
        $id_asignaturaVo = new AsignaturaId(1001);
        $this->ActividadAsignatura->setIdAsignaturaVo($id_asignaturaVo);
        $this->assertInstanceOf(AsignaturaId::class, $this->ActividadAsignatura->getIdAsignaturaVo());
        $this->assertEquals(1001, $this->ActividadAsignatura->getIdAsignaturaVo()->value());
    }

    public function test_set_and_get_id_profesor()
    {
        $this->ActividadAsignatura->setId_profesor(1);
        $this->assertEquals(1, $this->ActividadAsignatura->getId_profesor());
    }

    public function test_set_and_get_avis_profesor()
    {
        $avis_profesorVo = new AvisProfesor('test');
        $this->ActividadAsignatura->setAvisProfesorVo($avis_profesorVo);
        $this->assertInstanceOf(AvisProfesor::class, $this->ActividadAsignatura->getAvisProfesorVo());
        $this->assertEquals('test', $this->ActividadAsignatura->getAvisProfesorVo()->value());
    }

    public function test_set_and_get_tipo()
    {
        $tipoVo = new TipoActividadAsignatura('v');
        $this->ActividadAsignatura->setTipoVo($tipoVo);
        $this->assertInstanceOf(TipoActividadAsignatura::class, $this->ActividadAsignatura->getTipoVo());
        $this->assertEquals('v', $this->ActividadAsignatura->getTipoVo()->value());
    }

    public function test_set_and_get_df_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ActividadAsignatura->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ActividadAsignatura->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->ActividadAsignatura->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_df_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ActividadAsignatura->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ActividadAsignatura->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->ActividadAsignatura->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $actividadAsignatura = new ActividadAsignatura();
        $attributes = [
            'id_schema' => 1,
            'id_activ' => 1,
            'id_asignatura' => new AsignaturaId(1001),
            'id_profesor' => 1,
            'avis_profesor' => new AvisProfesor('test'),
            'tipo' => new TipoActividadAsignatura('v'),
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $actividadAsignatura->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadAsignatura->getId_schema());
        $this->assertEquals(1, $actividadAsignatura->getId_activ());
        $this->assertEquals(1001, $actividadAsignatura->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $actividadAsignatura->getId_profesor());
        $this->assertEquals('test', $actividadAsignatura->getAvisProfesorVo()->value());
        $this->assertEquals('v', $actividadAsignatura->getTipoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $actividadAsignatura->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $actividadAsignatura->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadAsignatura = new ActividadAsignatura();
        $attributes = [
            'id_schema' => 1,
            'id_activ' => 1,
            'id_asignatura' => 1001,
            'id_profesor' => 1,
            'avis_profesor' => 'test',
            'tipo' => 'v',
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $actividadAsignatura->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadAsignatura->getId_schema());
        $this->assertEquals(1, $actividadAsignatura->getId_activ());
        $this->assertEquals(1001, $actividadAsignatura->getIdAsignaturaVo()->value());
        $this->assertEquals(1, $actividadAsignatura->getId_profesor());
        $this->assertEquals('test', $actividadAsignatura->getAvisProfesorVo()->value());
        $this->assertEquals('v', $actividadAsignatura->getTipoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $actividadAsignatura->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $actividadAsignatura->getF_fin()->format('Y-m-d H:i:s'));
    }
}
