<?php

namespace Tests\unit\profesores\domain\entity;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\entity\ProfesorAmpliacion;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ProfesorAmpliacionTest extends myTest
{
    private ProfesorAmpliacion $ProfesorAmpliacion;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorAmpliacion = new ProfesorAmpliacion();
        $this->ProfesorAmpliacion->setId_item(1);
        $this->ProfesorAmpliacion->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorAmpliacion->setId_item(1);
        $this->assertEquals(1, $this->ProfesorAmpliacion->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorAmpliacion->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorAmpliacion->getId_nom());
    }

    public function test_set_and_get_id_asignatura()
    {
        $id_asignaturaVo = new AsignaturaId(1001);
        $this->ProfesorAmpliacion->setIdAsignaturaVo($id_asignaturaVo);
        $this->assertInstanceOf(AsignaturaId::class, $this->ProfesorAmpliacion->getIdAsignaturaVo());
        $this->assertEquals(1001, $this->ProfesorAmpliacion->getIdAsignaturaVo()->value());
    }

    public function test_set_and_get_escrito_nombramiento()
    {
        $escrito_nombramientoVo = new EscritoNombramiento('Test value');
        $this->ProfesorAmpliacion->setEscritoNombramientoVo($escrito_nombramientoVo);
        $this->assertInstanceOf(EscritoNombramiento::class, $this->ProfesorAmpliacion->getEscritoNombramientoVo());
        $this->assertEquals('Test value', $this->ProfesorAmpliacion->getEscritoNombramientoVo()->value());
    }

    public function test_set_and_get_f_nombramiento()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorAmpliacion->setF_nombramiento($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorAmpliacion->getF_nombramiento());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorAmpliacion->getF_nombramiento()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_escrito_cese()
    {
        $escrito_ceseVo = new EscritoCese('Test value');
        $this->ProfesorAmpliacion->setEscritoCeseVo($escrito_ceseVo);
        $this->assertInstanceOf(EscritoCese::class, $this->ProfesorAmpliacion->getEscritoCeseVo());
        $this->assertEquals('Test value', $this->ProfesorAmpliacion->getEscritoCeseVo()->value());
    }

    public function test_set_and_get_f_cese()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorAmpliacion->setF_cese($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorAmpliacion->getF_cese());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorAmpliacion->getF_cese()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes()
    {
        $profesorAmpliacion = new ProfesorAmpliacion();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_asignatura' => new AsignaturaId(1001),
            'escrito_nombramiento' => new EscritoNombramiento('Test value'),
            'f_nombramiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'escrito_cese' => new EscritoCese('Test value'),
            'f_cese' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorAmpliacion->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorAmpliacion->getId_item());
        $this->assertEquals(1, $profesorAmpliacion->getId_nom());
        $this->assertEquals(1001, $profesorAmpliacion->getIdAsignaturaVo()->value());
        $this->assertEquals('Test value', $profesorAmpliacion->getEscritoNombramientoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorAmpliacion->getF_nombramiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $profesorAmpliacion->getEscritoCeseVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorAmpliacion->getF_cese()->format('Y-m-d H:i:s'));
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorAmpliacion = new ProfesorAmpliacion();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'id_asignatura' => 1001,
            'escrito_nombramiento' => 'Test value',
            'f_nombramiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'escrito_cese' => 'Test value',
            'f_cese' => new DateTimeLocal('2024-01-15 10:30:00'),
        ];
        $profesorAmpliacion->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorAmpliacion->getId_item());
        $this->assertEquals(1, $profesorAmpliacion->getId_nom());
        $this->assertEquals(1001, $profesorAmpliacion->getIdAsignaturaVo()->value());
        $this->assertEquals('Test value', $profesorAmpliacion->getEscritoNombramientoVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorAmpliacion->getF_nombramiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $profesorAmpliacion->getEscritoCeseVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorAmpliacion->getF_cese()->format('Y-m-d H:i:s'));
    }
}
