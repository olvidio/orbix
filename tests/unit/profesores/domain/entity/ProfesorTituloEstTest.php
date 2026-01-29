<?php

namespace Tests\unit\profesores\domain\entity;

use src\profesores\domain\entity\ProfesorTituloEst;
use src\profesores\domain\value_objects\CentroDntName;
use src\profesores\domain\value_objects\PublicacionTitulo;
use src\profesores\domain\value_objects\TituloName;
use src\profesores\domain\value_objects\YearNumber;
use Tests\myTest;

class ProfesorTituloEstTest extends myTest
{
    private ProfesorTituloEst $ProfesorTituloEst;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorTituloEst = new ProfesorTituloEst();
        $this->ProfesorTituloEst->setId_item(1);
        $this->ProfesorTituloEst->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorTituloEst->setId_item(1);
        $this->assertEquals(1, $this->ProfesorTituloEst->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorTituloEst->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorTituloEst->getId_nom());
    }

    public function test_set_and_get_titulo()
    {
        $tituloVo = new PublicacionTitulo('test');
        $this->ProfesorTituloEst->setTituloVo($tituloVo);
        $this->assertInstanceOf(PublicacionTitulo::class, $this->ProfesorTituloEst->getTituloVo());
        $this->assertEquals('test', $this->ProfesorTituloEst->getTituloVo()->value());
    }

    public function test_set_and_get_centro_dnt()
    {
        $centro_dntVo = new CentroDntName('Test value');
        $this->ProfesorTituloEst->setCentroDntVo($centro_dntVo);
        $this->assertInstanceOf(CentroDntName::class, $this->ProfesorTituloEst->getCentroDntVo());
        $this->assertEquals('Test value', $this->ProfesorTituloEst->getCentroDntVo()->value());
    }

    public function test_set_and_get_eclesiastico()
    {
        $this->ProfesorTituloEst->setEclesiastico(true);
        $this->assertTrue($this->ProfesorTituloEst->isEclesiastico());
    }

    public function test_set_and_get_year()
    {
        $yearVo = new YearNumber(2024);
        $this->ProfesorTituloEst->setYearVo($yearVo);
        $this->assertInstanceOf(YearNumber::class, $this->ProfesorTituloEst->getYearVo());
        $this->assertEquals(2024, $this->ProfesorTituloEst->getYearVo()->value());
    }

    public function test_set_all_attributes()
    {
        $profesorTituloEst = new ProfesorTituloEst();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'titulo' => new PublicacionTitulo('test'),
            'centro_dnt' => new CentroDntName('Test value'),
            'eclesiastico' => true,
            'year' => new YearNumber(2024),
        ];
        $profesorTituloEst->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorTituloEst->getId_item());
        $this->assertEquals(1, $profesorTituloEst->getId_nom());
        $this->assertEquals('test', $profesorTituloEst->getTituloVo()->value());
        $this->assertEquals('Test value', $profesorTituloEst->getCentroDntVo()->value());
        $this->assertTrue($profesorTituloEst->isEclesiastico());
        $this->assertEquals(2024, $profesorTituloEst->getYearVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorTituloEst = new ProfesorTituloEst();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'titulo' => 'test',
            'centro_dnt' => 'Test value',
            'eclesiastico' => true,
            'year' => 2024,
        ];
        $profesorTituloEst->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorTituloEst->getId_item());
        $this->assertEquals(1, $profesorTituloEst->getId_nom());
        $this->assertEquals('test', $profesorTituloEst->getTituloVo()->value());
        $this->assertEquals('Test value', $profesorTituloEst->getCentroDntVo()->value());
        $this->assertTrue($profesorTituloEst->isEclesiastico());
        $this->assertEquals(2024, $profesorTituloEst->getYearVo()->value());
    }
}
