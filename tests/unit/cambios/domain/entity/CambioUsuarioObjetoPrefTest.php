<?php

namespace Tests\unit\cambios\domain\entity;

use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\cambios\domain\value_objects\CsvPauId;
use src\cambios\domain\value_objects\ObjetoNombre;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;

class CambioUsuarioObjetoPrefTest extends myTest
{
    private CambioUsuarioObjetoPref $CambioUsuarioObjetoPref;

    public function setUp(): void
    {
        parent::setUp();
        $this->CambioUsuarioObjetoPref = new CambioUsuarioObjetoPref();
        $this->CambioUsuarioObjetoPref->setId_item_usuario_objeto(1);
        $this->CambioUsuarioObjetoPref->setId_usuario(1);
    }

    public function test_set_and_get_id_item_usuario_objeto()
    {
        $this->CambioUsuarioObjetoPref->setId_item_usuario_objeto(1);
        $this->assertEquals(1, $this->CambioUsuarioObjetoPref->getId_item_usuario_objeto());
    }

    public function test_set_and_get_id_usuario()
    {
        $this->CambioUsuarioObjetoPref->setId_usuario(1);
        $this->assertEquals(1, $this->CambioUsuarioObjetoPref->getId_usuario());
    }

    public function test_set_and_get_dl_org()
    {
        $dl_orgVo = new DelegacionCode('Test');
        $this->CambioUsuarioObjetoPref->setDlOrgVo($dl_orgVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->CambioUsuarioObjetoPref->getDlOrgVo());
        $this->assertEquals('Test', $this->CambioUsuarioObjetoPref->getDlOrgVo()->value());
    }

    public function test_set_and_get_id_tipo_activ_txt()
    {
        $this->CambioUsuarioObjetoPref->setId_tipo_activ_txt('test');
        $this->assertEquals('test', $this->CambioUsuarioObjetoPref->getId_tipo_activ_txt());
    }

    public function test_set_and_get_id_fase_ref()
    {
        $this->CambioUsuarioObjetoPref->setId_fase_ref(1);
        $this->assertEquals(1, $this->CambioUsuarioObjetoPref->getId_fase_ref());
    }

    public function test_set_and_get_aviso_off()
    {
        $this->CambioUsuarioObjetoPref->setAviso_off(true);
        $this->assertTrue($this->CambioUsuarioObjetoPref->isAviso_off());
    }

    public function test_set_and_get_aviso_on()
    {
        $this->CambioUsuarioObjetoPref->setAviso_on(true);
        $this->assertTrue($this->CambioUsuarioObjetoPref->isAviso_on());
    }

    public function test_set_and_get_aviso_outdate()
    {
        $this->CambioUsuarioObjetoPref->setAviso_outdate(true);
        $this->assertTrue($this->CambioUsuarioObjetoPref->isAviso_outdate());
    }

    public function test_set_and_get_objeto()
    {
        $objetoVo = new ObjetoNombre('Test Name');
        $this->CambioUsuarioObjetoPref->setObjetoVo($objetoVo);
        $this->assertInstanceOf(ObjetoNombre::class, $this->CambioUsuarioObjetoPref->getObjetoVo());
        $this->assertEquals('Test Name', $this->CambioUsuarioObjetoPref->getObjetoVo()->value());
    }

    public function test_set_and_get_aviso_tipo()
    {
        $aviso_tipoVo = new AvisoTipoId(1);
        $this->CambioUsuarioObjetoPref->setAvisoTipoVo($aviso_tipoVo);
        $this->assertInstanceOf(AvisoTipoId::class, $this->CambioUsuarioObjetoPref->getAvisoTipoVo());
        $this->assertEquals(1, $this->CambioUsuarioObjetoPref->getAvisoTipoVo()->value());
    }

    public function test_set_and_get_csv_id_pau()
    {
        $csv_id_pauVo = new CsvPauId(1);
        $this->CambioUsuarioObjetoPref->setCsvIdPauVo($csv_id_pauVo);
        $this->assertInstanceOf(CsvPauId::class, $this->CambioUsuarioObjetoPref->getCsvIdPauVo());
        $this->assertEquals(1, $this->CambioUsuarioObjetoPref->getCsvIdPauVo()->value());
    }

    public function test_set_all_attributes()
    {
        $cambioUsuarioObjetoPref = new CambioUsuarioObjetoPref();
        $attributes = [
            'id_item_usuario_objeto' => 1,
            'id_usuario' => 1,
            'dl_org' => new DelegacionCode('Test'),
            'id_tipo_activ_txt' => 'test',
            'id_fase_ref' => 1,
            'aviso_off' => true,
            'aviso_on' => true,
            'aviso_outdate' => true,
            'objeto' => new ObjetoNombre('Test Name'),
            'aviso_tipo' => new AvisoTipoId(1),
            'csv_id_pau' => new CsvPauId(1),
        ];
        $cambioUsuarioObjetoPref->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioUsuarioObjetoPref->getId_item_usuario_objeto());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getId_usuario());
        $this->assertEquals('Test', $cambioUsuarioObjetoPref->getDlOrgVo()->value());
        $this->assertEquals('test', $cambioUsuarioObjetoPref->getId_tipo_activ_txt());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getId_fase_ref());
        $this->assertTrue($cambioUsuarioObjetoPref->isAviso_off());
        $this->assertTrue($cambioUsuarioObjetoPref->isAviso_on());
        $this->assertTrue($cambioUsuarioObjetoPref->isAviso_outdate());
        $this->assertEquals('Test Name', $cambioUsuarioObjetoPref->getObjetoVo()->value());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getAvisoTipoVo()->value());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getCsvIdPauVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $cambioUsuarioObjetoPref = new CambioUsuarioObjetoPref();
        $attributes = [
            'id_item_usuario_objeto' => 1,
            'id_usuario' => 1,
            'dl_org' => 'Test',
            'id_tipo_activ_txt' => 'test',
            'id_fase_ref' => 1,
            'aviso_off' => true,
            'aviso_on' => true,
            'aviso_outdate' => true,
            'objeto' => 'Test Name',
            'aviso_tipo' => 1,
            'csv_id_pau' => 1,
        ];
        $cambioUsuarioObjetoPref->setAllAttributes($attributes);

        $this->assertEquals(1, $cambioUsuarioObjetoPref->getId_item_usuario_objeto());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getId_usuario());
        $this->assertEquals('Test', $cambioUsuarioObjetoPref->getDlOrgVo()->value());
        $this->assertEquals('test', $cambioUsuarioObjetoPref->getId_tipo_activ_txt());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getId_fase_ref());
        $this->assertTrue($cambioUsuarioObjetoPref->isAviso_off());
        $this->assertTrue($cambioUsuarioObjetoPref->isAviso_on());
        $this->assertTrue($cambioUsuarioObjetoPref->isAviso_outdate());
        $this->assertEquals('Test Name', $cambioUsuarioObjetoPref->getObjetoVo()->value());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getAvisoTipoVo()->value());
        $this->assertEquals(1, $cambioUsuarioObjetoPref->getCsvIdPauVo()->value());
    }
}
