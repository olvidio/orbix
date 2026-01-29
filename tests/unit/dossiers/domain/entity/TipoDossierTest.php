<?php

namespace Tests\unit\dossiers\domain\entity;

use src\dossiers\domain\entity\TipoDossier;
use src\dossiers\domain\value_objects\TipoDossierApp;
use src\dossiers\domain\value_objects\TipoDossierCampoTo;
use src\dossiers\domain\value_objects\TipoDossierClass;
use src\dossiers\domain\value_objects\TipoDossierDb;
use src\dossiers\domain\value_objects\TipoDossierDescripcion;
use src\dossiers\domain\value_objects\TipoDossierTablaFrom;
use src\dossiers\domain\value_objects\TipoDossierTablaTo;
use Tests\myTest;

class TipoDossierTest extends myTest
{
    private TipoDossier $TipoDossier;

    public function setUp(): void
    {
        parent::setUp();
        $this->TipoDossier = new TipoDossier();
        $this->TipoDossier->setId_tipo_dossier(1);
        $this->TipoDossier->setTablaFromVo(new TipoDossierTablaFrom('a'));
    }

    public function test_set_and_get_id_tipo_dossier()
    {
        $this->TipoDossier->setId_tipo_dossier(1);
        $this->assertEquals(1, $this->TipoDossier->getId_tipo_dossier());
    }

    public function test_set_and_get_descripcion()
    {
        $descripcionVo = new TipoDossierDescripcion('test');
        $this->TipoDossier->setDescripcionVo($descripcionVo);
        $this->assertInstanceOf(TipoDossierDescripcion::class, $this->TipoDossier->getDescripcionVo());
        $this->assertEquals('test', $this->TipoDossier->getDescripcionVo()->value());
    }

    public function test_set_and_get_tabla_from()
    {
        $tabla_fromVo = new TipoDossierTablaFrom('a');
        $this->TipoDossier->setTablaFromVo($tabla_fromVo);
        $this->assertInstanceOf(TipoDossierTablaFrom::class, $this->TipoDossier->getTablaFromVo());
        $this->assertEquals('a', $this->TipoDossier->getTablaFromVo()->value());
    }

    public function test_set_and_get_tabla_to()
    {
        $tabla_toVo = new TipoDossierTablaTo('Test_value');
        $this->TipoDossier->setTablaToVo($tabla_toVo);
        $this->assertInstanceOf(TipoDossierTablaTo::class, $this->TipoDossier->getTablaToVo());
        $this->assertEquals('Test_value', $this->TipoDossier->getTablaToVo()->value());
    }

    public function test_set_and_get_campo_to()
    {
        $campo_toVo = new TipoDossierCampoTo('Test_value');
        $this->TipoDossier->setCampoToVo($campo_toVo);
        $this->assertInstanceOf(TipoDossierCampoTo::class, $this->TipoDossier->getCampoToVo());
        $this->assertEquals('Test_value', $this->TipoDossier->getCampoToVo()->value());
    }

    public function test_set_and_get_id_tipo_dossier_rel()
    {
        $this->TipoDossier->setId_tipo_dossier_rel(1);
        $this->assertEquals(1, $this->TipoDossier->getId_tipo_dossier_rel());
    }

    public function test_set_and_get_permiso_lectura()
    {
        $this->TipoDossier->setPermiso_lectura(1);
        $this->assertEquals(1, $this->TipoDossier->getPermiso_lectura());
    }

    public function test_set_and_get_permiso_escritura()
    {
        $this->TipoDossier->setPermiso_escritura(1);
        $this->assertEquals(1, $this->TipoDossier->getPermiso_escritura());
    }

    public function test_set_and_get_depende_modificar()
    {
        $this->TipoDossier->setDepende_modificar(true);
        $this->assertTrue($this->TipoDossier->isDepende_modificar());
    }

    public function test_set_and_get_app()
    {
        $appVo = new TipoDossierApp('Test value');
        $this->TipoDossier->setAppVo($appVo);
        $this->assertInstanceOf(TipoDossierApp::class, $this->TipoDossier->getAppVo());
        $this->assertEquals('Test value', $this->TipoDossier->getAppVo()->value());
    }

    public function test_set_and_get_class()
    {
        $classVo = new TipoDossierClass('Test value');
        $this->TipoDossier->setClassVo($classVo);
        $this->assertInstanceOf(TipoDossierClass::class, $this->TipoDossier->getClassVo());
        $this->assertEquals('Test value', $this->TipoDossier->getClassVo()->value());
    }

    public function test_set_and_get_db()
    {
        $dbVo = new TipoDossierDb(1);
        $this->TipoDossier->setDbVo($dbVo);
        $this->assertInstanceOf(TipoDossierDb::class, $this->TipoDossier->getDbVo());
        $this->assertEquals(1, $this->TipoDossier->getDbVo()->value());
    }

    public function test_set_all_attributes()
    {
        $tipoDossier = new TipoDossier();
        $attributes = [
            'id_tipo_dossier' => 1,
            'descripcion' => new TipoDossierDescripcion('test'),
            'tabla_from' => new TipoDossierTablaFrom('a'),
            'tabla_to' => new TipoDossierTablaTo('Test_value'),
            'campo_to' => new TipoDossierCampoTo('Test_value'),
            'id_tipo_dossier_rel' => 1,
            'permiso_lectura' => 1,
            'permiso_escritura' => 1,
            'depende_modificar' => true,
            'app' => new TipoDossierApp('Test value'),
            'class' => new TipoDossierClass('Test value'),
            'db' => new TipoDossierDb(1),
        ];
        $tipoDossier->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoDossier->getId_tipo_dossier());
        $this->assertEquals('test', $tipoDossier->getDescripcionVo()->value());
        $this->assertEquals('a', $tipoDossier->getTablaFromVo()->value());
        $this->assertEquals('Test_value', $tipoDossier->getTablaToVo()->value());
        $this->assertEquals('Test_value', $tipoDossier->getCampoToVo()->value());
        $this->assertEquals(1, $tipoDossier->getId_tipo_dossier_rel());
        $this->assertEquals(1, $tipoDossier->getPermiso_lectura());
        $this->assertEquals(1, $tipoDossier->getPermiso_escritura());
        $this->assertTrue($tipoDossier->isDepende_modificar());
        $this->assertEquals('Test value', $tipoDossier->getAppVo()->value());
        $this->assertEquals('Test value', $tipoDossier->getClassVo()->value());
        $this->assertEquals(1, $tipoDossier->getDbVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tipoDossier = new TipoDossier();
        $attributes = [
            'id_tipo_dossier' => 1,
            'descripcion' => 'test',
            'tabla_from' => 'a',
            'tabla_to' => 'Test_value',
            'campo_to' => 'Test_value',
            'id_tipo_dossier_rel' => 1,
            'permiso_lectura' => 1,
            'permiso_escritura' => 1,
            'depende_modificar' => true,
            'app' => 'Test value',
            'class' => 'Test value',
            'db' => 1,
        ];
        $tipoDossier->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoDossier->getId_tipo_dossier());
        $this->assertEquals('test', $tipoDossier->getDescripcionVo()->value());
        $this->assertEquals('a', $tipoDossier->getTablaFromVo()->value());
        $this->assertEquals('Test_value', $tipoDossier->getTablaToVo()->value());
        $this->assertEquals('Test_value', $tipoDossier->getCampoToVo()->value());
        $this->assertEquals(1, $tipoDossier->getId_tipo_dossier_rel());
        $this->assertEquals(1, $tipoDossier->getPermiso_lectura());
        $this->assertEquals(1, $tipoDossier->getPermiso_escritura());
        $this->assertTrue($tipoDossier->isDepende_modificar());
        $this->assertEquals('Test value', $tipoDossier->getAppVo()->value());
        $this->assertEquals('Test value', $tipoDossier->getClassVo()->value());
        $this->assertEquals(1, $tipoDossier->getDbVo()->value());
    }
}
