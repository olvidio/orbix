<?php

namespace Tests\unit\inventario\domain\entity;

use src\inventario\domain\entity\Documento;
use src\inventario\domain\value_objects\DocumentoId;
use src\inventario\domain\value_objects\DocumentoIdentificador;
use src\inventario\domain\value_objects\DocumentoNumEjemplares;
use src\inventario\domain\value_objects\DocumentoNumFin;
use src\inventario\domain\value_objects\DocumentoNumIni;
use src\inventario\domain\value_objects\DocumentoNumReg;
use src\inventario\domain\value_objects\DocumentoObserv;
use src\inventario\domain\value_objects\DocumentoObservCtr;
use src\inventario\domain\value_objects\LugarId;
use src\inventario\domain\value_objects\TipoDocId;
use src\inventario\domain\value_objects\UbiInventarioId;
use Tests\myTest;

class DocumentoTest extends myTest
{
    private Documento $Documento;

    public function setUp(): void
    {
        parent::setUp();
        $this->Documento = new Documento();
        $this->Documento->setIdDocVo(new DocumentoId(1));
        $this->Documento->setIdTipoDocVo(new TipoDocId(1));
    }

    public function test_set_and_get_id_doc()
    {
        $id_docVo = new DocumentoId(1);
        $this->Documento->setIdDocVo($id_docVo);
        $this->assertInstanceOf(DocumentoId::class, $this->Documento->getIdDocVo());
        $this->assertEquals(1, $this->Documento->getIdDocVo()->value());
    }

    public function test_set_and_get_id_tipo_doc()
    {
        $id_tipo_docVo = new TipoDocId(1);
        $this->Documento->setIdTipoDocVo($id_tipo_docVo);
        $this->assertInstanceOf(TipoDocId::class, $this->Documento->getIdTipoDocVo());
        $this->assertEquals(1, $this->Documento->getIdTipoDocVo()->value());
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new UbiInventarioId(1);
        $this->Documento->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(UbiInventarioId::class, $this->Documento->getIdUbiVo());
        $this->assertEquals(1, $this->Documento->getIdUbiVo()->value());
    }

    public function test_set_and_get_id_lugar()
    {
        $id_lugarVo = new LugarId(1);
        $this->Documento->setIdLugarVo($id_lugarVo);
        $this->assertInstanceOf(LugarId::class, $this->Documento->getIdLugarVo());
        $this->assertEquals(1, $this->Documento->getIdLugarVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new DocumentoObserv('Test value');
        $this->Documento->setObservVo($observVo);
        $this->assertInstanceOf(DocumentoObserv::class, $this->Documento->getObservVo());
        $this->assertEquals('Test value', $this->Documento->getObservVo()->value());
    }

    public function test_set_and_get_observCtr()
    {
        $observCtrVo = new DocumentoObservCtr('Test value');
        $this->Documento->setObservCtrVo($observCtrVo);
        $this->assertInstanceOf(DocumentoObservCtr::class, $this->Documento->getObservCtrVo());
        $this->assertEquals('Test value', $this->Documento->getObservCtrVo()->value());
    }

    public function test_set_and_get_en_busqueda()
    {
        $this->Documento->setEnBusquedaVo(true);
        $this->assertEquals(true, $this->Documento->isEnBusqueda());
    }

    public function test_set_and_get_perdido()
    {
        $this->Documento->setPerdidoVo(true);
        $this->assertEquals(true, $this->Documento->isPerdido());
    }

    public function test_set_and_get_eliminado()
    {
        $this->Documento->setEliminadoVo(true);
        $this->assertEquals(true, $this->Documento->isEliminado());
    }

    public function test_set_and_get_num_reg()
    {
        $num_regVo = new DocumentoNumReg(1);
        $this->Documento->setNumRegVo($num_regVo);
        $this->assertInstanceOf(DocumentoNumReg::class, $this->Documento->getNumRegVo());
        $this->assertEquals(1, $this->Documento->getNumRegVo()->value());
    }

    public function test_set_and_get_num_ini()
    {
        $num_iniVo = new DocumentoNumIni(1);
        $this->Documento->setNumIniVo($num_iniVo);
        $this->assertInstanceOf(DocumentoNumIni::class, $this->Documento->getNumIniVo());
        $this->assertEquals(1, $this->Documento->getNumIniVo()->value());
    }

    public function test_set_and_get_num_fin()
    {
        $num_finVo = new DocumentoNumFin(1);
        $this->Documento->setNumFinVo($num_finVo);
        $this->assertInstanceOf(DocumentoNumFin::class, $this->Documento->getNumFinVo());
        $this->assertEquals(1, $this->Documento->getNumFinVo()->value());
    }

    public function test_set_and_get_identificador()
    {
        $identificadorVo = new DocumentoIdentificador('Test value');
        $this->Documento->setIdentificadorVo($identificadorVo);
        $this->assertInstanceOf(DocumentoIdentificador::class, $this->Documento->getIdentificadorVo());
        $this->assertEquals('Test value', $this->Documento->getIdentificadorVo()->value());
    }

    public function test_set_and_get_num_ejemplares()
    {
        $num_ejemplaresVo = new DocumentoNumEjemplares(1);
        $this->Documento->setNumEjemplaresVo($num_ejemplaresVo);
        $this->assertInstanceOf(DocumentoNumEjemplares::class, $this->Documento->getNumEjemplaresVo());
        $this->assertEquals(1, $this->Documento->getNumEjemplaresVo()->value());
    }

    public function test_set_all_attributes()
    {
        $documento = new Documento();
        $attributes = [
            'id_doc' => new DocumentoId(1),
            'id_tipo_doc' => new TipoDocId(1),
            'id_ubi' => new UbiInventarioId(1),
            'id_lugar' => new LugarId(1),
            'observ' => new DocumentoObserv('Test value'),
            'observCtr' => new DocumentoObservCtr('Test value'),
            'en_busqueda' => true,
            'perdido' => true,
            'eliminado' => true,
            'num_reg' => new DocumentoNumReg(1),
            'num_ini' => new DocumentoNumIni(1),
            'num_fin' => new DocumentoNumFin(1),
            'identificador' => new DocumentoIdentificador('Test value'),
            'num_ejemplares' => new DocumentoNumEjemplares(1),
        ];
        $documento->setAllAttributes($attributes);

        $this->assertEquals(1, $documento->getIdDocVo()->value());
        $this->assertEquals(1, $documento->getIdTipoDocVo()->value());
        $this->assertEquals(1, $documento->getIdUbiVo()->value());
        $this->assertEquals(1, $documento->getIdLugarVo()->value());
        $this->assertEquals('Test value', $documento->getObservVo()->value());
        $this->assertEquals('Test value', $documento->getObservCtrVo()->value());
        $this->assertEquals(true, $documento->isEnBusqueda());
        $this->assertEquals(true, $documento->isPerdido());
        $this->assertEquals(true, $documento->isEliminado());
        $this->assertEquals(1, $documento->getNumRegVo()->value());
        $this->assertEquals(1, $documento->getNumIniVo()->value());
        $this->assertEquals(1, $documento->getNumFinVo()->value());
        $this->assertEquals('Test value', $documento->getIdentificadorVo()->value());
        $this->assertEquals(1, $documento->getNumEjemplaresVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $documento = new Documento();
        $attributes = [
            'id_doc' => 1,
            'id_tipo_doc' => 1,
            'id_ubi' => 1,
            'id_lugar' => 1,
            'observ' => 'Test value',
            'observCtr' => 'Test value',
            'en_busqueda' => true,
            'perdido' => true,
            'eliminado' => true,
            'num_reg' => 1,
            'num_ini' => 1,
            'num_fin' => 1,
            'identificador' => 'Test value',
            'num_ejemplares' => 1,
        ];
        $documento->setAllAttributes($attributes);

        $this->assertEquals(1, $documento->getIdDocVo()->value());
        $this->assertEquals(1, $documento->getIdTipoDocVo()->value());
        $this->assertEquals(1, $documento->getIdUbiVo()->value());
        $this->assertEquals(1, $documento->getIdLugarVo()->value());
        $this->assertEquals('Test value', $documento->getObservVo()->value());
        $this->assertEquals('Test value', $documento->getObservCtrVo()->value());
        $this->assertEquals(true, $documento->isEnBusqueda());
        $this->assertEquals(true, $documento->isPerdido());
        $this->assertEquals(true, $documento->isEliminado());
        $this->assertEquals(1, $documento->getNumRegVo()->value());
        $this->assertEquals(1, $documento->getNumIniVo()->value());
        $this->assertEquals(1, $documento->getNumFinVo()->value());
        $this->assertEquals('Test value', $documento->getIdentificadorVo()->value());
        $this->assertEquals(1, $documento->getNumEjemplaresVo()->value());
    }
}
