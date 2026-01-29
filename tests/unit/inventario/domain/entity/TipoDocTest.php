<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\value_objects\ColeccionId;
use src\inventario\domain\value_objects\TipoDocBajoLlave;
use src\inventario\domain\value_objects\TipoDocId;
use src\inventario\domain\value_objects\TipoDocName;
use src\inventario\domain\value_objects\TipoDocNumerado;
use src\inventario\domain\value_objects\TipoDocObserv;
use src\inventario\domain\value_objects\TipoDocSigla;
use src\inventario\domain\value_objects\TipoDocVigente;
use Tests\myTest;

class TipoDocTest extends myTest
{
    private TipoDoc $TipoDoc;

    public function setUp(): void
    {
        parent::setUp();
        $this->TipoDoc = new TipoDoc();
        $this->TipoDoc->setIdTipoDocVo(new TipoDocId(1));
        $this->TipoDoc->setSiglaVo(new TipoDocSigla('Test value'));
    }

    public function test_set_and_get_id_tipo_doc()
    {
        $id_tipo_docVo = new TipoDocId(1);
        $this->TipoDoc->setIdTipoDocVo($id_tipo_docVo);
        $this->assertInstanceOf(TipoDocId::class, $this->TipoDoc->getIdTipoDocVo());
        $this->assertEquals(1, $this->TipoDoc->getIdTipoDocVo()->value());
    }

    public function test_set_and_get_nom_doc()
    {
        $nom_docVo = new TipoDocName('Test value');
        $this->TipoDoc->setNomDocVo($nom_docVo);
        $this->assertInstanceOf(TipoDocName::class, $this->TipoDoc->getNomDocVo());
        $this->assertEquals('Test value', $this->TipoDoc->getNomDocVo()->value());
    }

    public function test_set_and_get_sigla()
    {
        $siglaVo = new TipoDocSigla('Test value');
        $this->TipoDoc->setSiglaVo($siglaVo);
        $this->assertInstanceOf(TipoDocSigla::class, $this->TipoDoc->getSiglaVo());
        $this->assertEquals('Test value', $this->TipoDoc->getSiglaVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new TipoDocObserv('test');
        $this->TipoDoc->setObservVo($observVo);
        $this->assertInstanceOf(TipoDocObserv::class, $this->TipoDoc->getObservVo());
        $this->assertEquals('test', $this->TipoDoc->getObservVo()->value());
    }

    public function test_set_and_get_id_coleccion()
    {
        $id_coleccionVo = new ColeccionId(1);
        $this->TipoDoc->setIdColeccionVo($id_coleccionVo);
        $this->assertInstanceOf(ColeccionId::class, $this->TipoDoc->getIdColeccionVo());
        $this->assertEquals(1, $this->TipoDoc->getIdColeccionVo()->value());
    }

    public function test_set_and_get_bajo_llave()
    {
        $this->TipoDoc->setBajo_llave(true);
        $this->assertTrue($this->TipoDoc->isBajo_llave());
    }

    public function test_set_and_get_vigente()
    {
        $this->TipoDoc->setVigente(true);
        $this->assertTrue($this->TipoDoc->isVigente());
    }

    public function test_set_and_get_numerado()
    {
        $this->TipoDoc->setNumerado(true);
        $this->assertTrue($this->TipoDoc->isNumerado());
    }

    public function test_set_all_attributes()
    {
        $tipoDoc = new TipoDoc();
        $attributes = [
            'id_tipo_doc' => new TipoDocId(1),
            'nom_doc' => new TipoDocName('Test value'),
            'sigla' => new TipoDocSigla('Test value'),
            'observ' => new TipoDocObserv('test'),
            'id_coleccion' => new ColeccionId(1),
            'bajo_llave' => true,
            'vigente' => true,
            'numerado' => true,
        ];
        $tipoDoc->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoDoc->getIdTipoDocVo()->value());
        $this->assertEquals('Test value', $tipoDoc->getNomDocVo()->value());
        $this->assertEquals('Test value', $tipoDoc->getSiglaVo()->value());
        $this->assertEquals('test', $tipoDoc->getObservVo()->value());
        $this->assertEquals(1, $tipoDoc->getIdColeccionVo()->value());
        $this->assertTrue($tipoDoc->isBajo_llave());
        $this->assertTrue($tipoDoc->isVigente());
        $this->assertTrue($tipoDoc->isNumerado());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $tipoDoc = new TipoDoc();
        $attributes = [
            'id_tipo_doc' => 1,
            'nom_doc' => 'Test value',
            'sigla' => 'Test value',
            'observ' => 'test',
            'id_coleccion' => 1,
            'bajo_llave' => true,
            'vigente' => true,
            'numerado' => true,
        ];
        $tipoDoc->setAllAttributes($attributes);

        $this->assertEquals(1, $tipoDoc->getIdTipoDocVo()->value());
        $this->assertEquals('Test value', $tipoDoc->getNomDocVo()->value());
        $this->assertEquals('Test value', $tipoDoc->getSiglaVo()->value());
        $this->assertEquals('test', $tipoDoc->getObservVo()->value());
        $this->assertEquals(1, $tipoDoc->getIdColeccionVo()->value());
        $this->assertTrue($tipoDoc->isBajo_llave());
        $this->assertTrue($tipoDoc->isVigente());
        $this->assertTrue($tipoDoc->isNumerado());
    }
}
