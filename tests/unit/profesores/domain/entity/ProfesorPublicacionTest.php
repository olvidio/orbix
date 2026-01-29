<?php

namespace Tests\unit\profesores\domain\entity;

use src\profesores\domain\entity\ProfesorPublicacion;
use src\profesores\domain\value_objects\ColeccionName;
use src\profesores\domain\value_objects\EditorialName;
use src\profesores\domain\value_objects\LugarPublicacionName;
use src\profesores\domain\value_objects\ObservacionText;
use src\profesores\domain\value_objects\PublicacionTitulo;
use src\profesores\domain\value_objects\ReferenciaText;
use src\profesores\domain\value_objects\TipoPublicacionName;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

class ProfesorPublicacionTest extends myTest
{
    private ProfesorPublicacion $ProfesorPublicacion;

    public function setUp(): void
    {
        parent::setUp();
        $this->ProfesorPublicacion = new ProfesorPublicacion();
        $this->ProfesorPublicacion->setId_item(1);
        $this->ProfesorPublicacion->setId_nom(1);
    }

    public function test_set_and_get_id_item()
    {
        $this->ProfesorPublicacion->setId_item(1);
        $this->assertEquals(1, $this->ProfesorPublicacion->getId_item());
    }

    public function test_set_and_get_id_nom()
    {
        $this->ProfesorPublicacion->setId_nom(1);
        $this->assertEquals(1, $this->ProfesorPublicacion->getId_nom());
    }

    public function test_set_and_get_tipo_publicacion()
    {
        $tipo_publicacionVo = new TipoPublicacionName('Test value');
        $this->ProfesorPublicacion->setTipoPublicacionVo($tipo_publicacionVo);
        $this->assertInstanceOf(TipoPublicacionName::class, $this->ProfesorPublicacion->getTipoPublicacionVo());
        $this->assertEquals('Test value', $this->ProfesorPublicacion->getTipoPublicacionVo()->value());
    }

    public function test_set_and_get_titulo()
    {
        $tituloVo = new PublicacionTitulo('test');
        $this->ProfesorPublicacion->setTituloVo($tituloVo);
        $this->assertInstanceOf(PublicacionTitulo::class, $this->ProfesorPublicacion->getTituloVo());
        $this->assertEquals('test', $this->ProfesorPublicacion->getTituloVo()->value());
    }

    public function test_set_and_get_editorial()
    {
        $editorialVo = new EditorialName('Test value');
        $this->ProfesorPublicacion->setEditorialVo($editorialVo);
        $this->assertInstanceOf(EditorialName::class, $this->ProfesorPublicacion->getEditorialVo());
        $this->assertEquals('Test value', $this->ProfesorPublicacion->getEditorialVo()->value());
    }

    public function test_set_and_get_coleccion()
    {
        $coleccionVo = new ColeccionName('Test value');
        $this->ProfesorPublicacion->setColeccionVo($coleccionVo);
        $this->assertInstanceOf(ColeccionName::class, $this->ProfesorPublicacion->getColeccionVo());
        $this->assertEquals('Test value', $this->ProfesorPublicacion->getColeccionVo()->value());
    }

    public function test_set_and_get_f_publicacion()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ProfesorPublicacion->setF_publicacion($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ProfesorPublicacion->getF_publicacion());
        $this->assertEquals('2024-01-15 10:30:00', $this->ProfesorPublicacion->getF_publicacion()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_pendiente()
    {
        $this->ProfesorPublicacion->setPendiente(true);
        $this->assertTrue($this->ProfesorPublicacion->isPendiente());
    }

    public function test_set_and_get_referencia()
    {
        $referenciaVo = new ReferenciaText('Test value');
        $this->ProfesorPublicacion->setReferenciaVo($referenciaVo);
        $this->assertInstanceOf(ReferenciaText::class, $this->ProfesorPublicacion->getReferenciaVo());
        $this->assertEquals('Test value', $this->ProfesorPublicacion->getReferenciaVo()->value());
    }

    public function test_set_and_get_lugar()
    {
        $lugarVo = new LugarPublicacionName('Test Name');
        $this->ProfesorPublicacion->setLugarVo($lugarVo);
        $this->assertInstanceOf(LugarPublicacionName::class, $this->ProfesorPublicacion->getLugarVo());
        $this->assertEquals('Test Name', $this->ProfesorPublicacion->getLugarVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservacionText('Test');
        $this->ProfesorPublicacion->setObservVo($observVo);
        $this->assertInstanceOf(ObservacionText::class, $this->ProfesorPublicacion->getObservVo());
        $this->assertEquals('Test', $this->ProfesorPublicacion->getObservVo()->value());
    }

    public function test_set_all_attributes()
    {
        $profesorPublicacion = new ProfesorPublicacion();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'tipo_publicacion' => new TipoPublicacionName('Test value'),
            'titulo' => new PublicacionTitulo('test'),
            'editorial' => new EditorialName('Test value'),
            'coleccion' => new ColeccionName('Test value'),
            'f_publicacion' => new DateTimeLocal('2024-01-15 10:30:00'),
            'pendiente' => true,
            'referencia' => new ReferenciaText('Test value'),
            'lugar' => new LugarPublicacionName('Test Name'),
            'observ' => new ObservacionText('Test'),
        ];
        $profesorPublicacion->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorPublicacion->getId_item());
        $this->assertEquals(1, $profesorPublicacion->getId_nom());
        $this->assertEquals('Test value', $profesorPublicacion->getTipoPublicacionVo()->value());
        $this->assertEquals('test', $profesorPublicacion->getTituloVo()->value());
        $this->assertEquals('Test value', $profesorPublicacion->getEditorialVo()->value());
        $this->assertEquals('Test value', $profesorPublicacion->getColeccionVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorPublicacion->getF_publicacion()->format('Y-m-d H:i:s'));
        $this->assertTrue($profesorPublicacion->isPendiente());
        $this->assertEquals('Test value', $profesorPublicacion->getReferenciaVo()->value());
        $this->assertEquals('Test Name', $profesorPublicacion->getLugarVo()->value());
        $this->assertEquals('Test', $profesorPublicacion->getObservVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $profesorPublicacion = new ProfesorPublicacion();
        $attributes = [
            'id_item' => 1,
            'id_nom' => 1,
            'tipo_publicacion' => 'Test value',
            'titulo' => 'test',
            'editorial' => 'Test value',
            'coleccion' => 'Test value',
            'f_publicacion' => new DateTimeLocal('2024-01-15 10:30:00'),
            'pendiente' => true,
            'referencia' => 'Test value',
            'lugar' => 'Test Name',
            'observ' => 'Test',
        ];
        $profesorPublicacion->setAllAttributes($attributes);

        $this->assertEquals(1, $profesorPublicacion->getId_item());
        $this->assertEquals(1, $profesorPublicacion->getId_nom());
        $this->assertEquals('Test value', $profesorPublicacion->getTipoPublicacionVo()->value());
        $this->assertEquals('test', $profesorPublicacion->getTituloVo()->value());
        $this->assertEquals('Test value', $profesorPublicacion->getEditorialVo()->value());
        $this->assertEquals('Test value', $profesorPublicacion->getColeccionVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $profesorPublicacion->getF_publicacion()->format('Y-m-d H:i:s'));
        $this->assertTrue($profesorPublicacion->isPendiente());
        $this->assertEquals('Test value', $profesorPublicacion->getReferenciaVo()->value());
        $this->assertEquals('Test Name', $profesorPublicacion->getLugarVo()->value());
        $this->assertEquals('Test', $profesorPublicacion->getObservVo()->value());
    }
}
