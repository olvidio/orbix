<?php

namespace Tests\unit\profesores\domain\entity;

use src\profesores\domain\entity\ProfesorCongreso;
use src\profesores\domain\value_objects\CongresoName;
use src\profesores\domain\value_objects\CongresoTipo;
use src\profesores\domain\value_objects\LugarName;
use src\profesores\domain\value_objects\OrganizaName;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ProfesorCongresoTest extends myTest
{
    private ProfesorCongreso $ProfesorCongreso;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorCongreso = new ProfesorCongreso();
        $this->ProfesorCongreso->setId_item(1);
        $this->ProfesorCongreso->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorCongreso->setId_item(1);
        $this->assertEquals(1, $this->ProfesorCongreso->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorCongreso->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorCongreso->getId_nom());
    }

    public function test_set_and_get_congreso()
    {
        $congresoVo = new CongresoName('Test Name');
        $this->ProfesorCongreso->setCongresoVo($congresoVo);
        $this->assertInstanceOf(CongresoName::class, $this->ProfesorCongreso->getCongresoVo());
        $this->assertEquals('Test Name', $this->ProfesorCongreso->getCongresoVo()->value());
    }

    public function test_set_and_get_lugar()
    {
        $lugarVo = new LugarName('Test value');
        $this->ProfesorCongreso->setLugarVo($lugarVo);
        $this->assertInstanceOf(LugarName::class, $this->ProfesorCongreso->getLugarVo());
        $this->assertEquals('Test value', $this->ProfesorCongreso->getLugarVo()->value());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorCongreso->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorCongreso->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorCongreso->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_f_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorCongreso->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorCongreso->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorCongreso->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_organiza()
    {
        $organizaVo = new OrganizaName('Test value');
        $this->ProfesorCongreso->setOrganizaVo($organizaVo);
        $this->assertInstanceOf(OrganizaName::class, $this->ProfesorCongreso->getOrganizaVo());
        $this->assertEquals('Test value', $this->ProfesorCongreso->getOrganizaVo()->value());
    }

    public function test_set_and_get_tipo()
    {
        $tipoVo = new CongresoTipo(1);
        $this->ProfesorCongreso->setTipoVo($tipoVo);
        $this->assertInstanceOf(CongresoTipo::class, $this->ProfesorCongreso->getTipoVo());
        $this->assertEquals(1, $this->ProfesorCongreso->getTipoVo()->value());
    }

    public function test_set_all_attributes()
    {
        $profesorCongreso = new ProfesorCongreso();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'congreso' => new CongresoName('Test Name'),
            'lugar' => new LugarName('Test value'),
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'organiza' => new OrganizaName('Test value'),
            'tipo' => new CongresoTipo(1),
        ];
        $profesorCongreso->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorCongreso->getId_item());
        $this->assertEquals(1, $profesorCongreso->getId_nom());
        $this->assertEquals('Test Name', $profesorCongreso->getCongresoVo()->value());
        $this->assertEquals('Test value', $profesorCongreso->getLugarVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorCongreso->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $profesorCongreso->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $profesorCongreso->getOrganizaVo()->value());
        $this->assertEquals(1, $profesorCongreso->getTipoVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorCongreso = new ProfesorCongreso();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'congreso' => 'Test Name',
            'lugar' => 'Test value',
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'organiza' => 'Test value',
            'tipo' => 1,
        ];
        $profesorCongreso->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorCongreso->getId_item());
        $this->assertEquals(1, $profesorCongreso->getId_nom());
        $this->assertEquals('Test Name', $profesorCongreso->getCongresoVo()->value());
        $this->assertEquals('Test value', $profesorCongreso->getLugarVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorCongreso->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-15 10:30:00', $profesorCongreso->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $profesorCongreso->getOrganizaVo()->value());
        $this->assertEquals(1, $profesorCongreso->getTipoVo()->value());
    }
}
