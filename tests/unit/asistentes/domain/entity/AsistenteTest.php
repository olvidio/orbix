<?php

namespace Tests\unit\asistentes\domain\entity;

use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistenteEncargo;
use src\asistentes\domain\value_objects\AsistenteObserv;
use src\asistentes\domain\value_objects\AsistenteObservEst;
use src\asistentes\domain\value_objects\AsistentePropietario;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;

class AsistenteTest extends myTest
{
    private Asistente $Asistente;

    public function setUp(): void
    {
        parent::setUp();
        $this->Asistente = new Asistente();
        $this->Asistente->setId_activ(1);
        $this->Asistente->setId_nom(1);
    }

    public function test_set_and_get_id_activ()
    {
        $this->Asistente->setId_activ(1);
        $this->assertEquals(1, $this->Asistente->getId_activ());
    }

    public function test_set_and_get_id_nom()
    {
        $this->Asistente->setId_nom(1);
        $this->assertEquals(1, $this->Asistente->getId_nom());
    }

    public function test_set_and_get_propio()
    {
        $this->Asistente->setPropio(true);
        $this->assertTrue($this->Asistente->isPropio());
    }

    public function test_set_and_get_est_ok()
    {
        $this->Asistente->setEst_ok(true);
        $this->assertTrue($this->Asistente->isEst_ok());
    }

    public function test_set_and_get_cfi()
    {
        $this->Asistente->setCfi(true);
        $this->assertTrue($this->Asistente->isCfi());
    }

    public function test_set_and_get_cfi_con()
    {
        $this->Asistente->setCfi_con(1);
        $this->assertEquals(1, $this->Asistente->getCfi_con());
    }

    public function test_set_and_get_falta()
    {
        $this->Asistente->setFalta(true);
        $this->assertTrue($this->Asistente->isFalta());
    }

    public function test_set_and_get_encargo()
    {
        $encargoVo = new AsistenteEncargo('test');
        $this->Asistente->setEncargoVo($encargoVo);
        $this->assertInstanceOf(AsistenteEncargo::class, $this->Asistente->getEncargoVo());
        $this->assertEquals('test', $this->Asistente->getEncargoVo()->value());
    }

    public function test_set_and_get_dl_responsable()
    {
        $dl_responsableVo = new DelegacionCode('Test');
        $this->Asistente->setDlResponsableVo($dl_responsableVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->Asistente->getDlResponsableVo());
        $this->assertEquals('Test', $this->Asistente->getDlResponsableVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new AsistenteObserv('test');
        $this->Asistente->setObservVo($observVo);
        $this->assertInstanceOf(AsistenteObserv::class, $this->Asistente->getObservVo());
        $this->assertEquals('test', $this->Asistente->getObservVo()->value());
    }

    public function test_set_and_get_id_tabla()
    {
        $id_tablaVo = new PersonaTablaCode('Test');
        $this->Asistente->setIdTablaVo($id_tablaVo);
        $this->assertInstanceOf(PersonaTablaCode::class, $this->Asistente->getIdTablaVo());
        $this->assertEquals('Test', $this->Asistente->getIdTablaVo()->value());
    }

    public function test_set_and_get_plaza()
    {
        $plazaVo = new PlazaId(1);
        $this->Asistente->setPlazaVo($plazaVo);
        $this->assertInstanceOf(PlazaId::class, $this->Asistente->getPlazaVo());
        $this->assertEquals(1, $this->Asistente->getPlazaVo()->value());
    }

    public function test_set_and_get_propietario()
    {
        $propietarioVo = new AsistentePropietario('test');
        $this->Asistente->setPropietarioVo($propietarioVo);
        $this->assertInstanceOf(AsistentePropietario::class, $this->Asistente->getPropietarioVo());
        $this->assertEquals('test', $this->Asistente->getPropietarioVo()->value());
    }

    public function test_set_and_get_observ_est()
    {
        $observ_estVo = new AsistenteObservEst('test');
        $this->Asistente->setObservEstVo($observ_estVo);
        $this->assertInstanceOf(AsistenteObservEst::class, $this->Asistente->getObservEstVo());
        $this->assertEquals('test', $this->Asistente->getObservEstVo()->value());
    }

    public function test_set_all_attributes()
    {
        $asistente = new Asistente();
        $attributes = [
            'id_activ' => 1,
            'id_nom' => 1,
            'propio' => true,
            'est_ok' => true,
            'cfi' => true,
            'cfi_con' => 1,
            'falta' => true,
            'encargo' => new AsistenteEncargo('test'),
            'dl_responsable' => new DelegacionCode('Test'),
            'observ' => new AsistenteObserv('test'),
            'id_tabla' => new PersonaTablaCode('Test'),
            'plaza' => new PlazaId(1),
            'propietario' => new AsistentePropietario('test'),
            'observ_est' => new AsistenteObservEst('test'),
        ];
        $asistente->setAllAttributes($attributes);

        $this->assertEquals(1, $asistente->getId_activ());
        $this->assertEquals(1, $asistente->getId_nom());
        $this->assertTrue($asistente->isPropio());
        $this->assertTrue($asistente->isEst_ok());
        $this->assertTrue($asistente->isCfi());
        $this->assertEquals(1, $asistente->getCfi_con());
        $this->assertTrue($asistente->isFalta());
        $this->assertEquals('test', $asistente->getEncargoVo()->value());
        $this->assertEquals('Test', $asistente->getDlResponsableVo()->value());
        $this->assertEquals('test', $asistente->getObservVo()->value());
        $this->assertEquals('Test', $asistente->getIdTablaVo()->value());
        $this->assertEquals(1, $asistente->getPlazaVo()->value());
        $this->assertEquals('test', $asistente->getPropietarioVo()->value());
        $this->assertEquals('test', $asistente->getObservEstVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $asistente = new Asistente();
        $attributes = [
            'id_activ' => 1,
            'id_nom' => 1,
            'propio' => true,
            'est_ok' => true,
            'cfi' => true,
            'cfi_con' => 1,
            'falta' => true,
            'encargo' => 'test',
            'dl_responsable' => 'Test',
            'observ' => 'test',
            'id_tabla' => 'Test',
            'plaza' => 1,
            'propietario' => 'test',
            'observ_est' => 'test',
        ];
        $asistente->setAllAttributes($attributes);

        $this->assertEquals(1, $asistente->getId_activ());
        $this->assertEquals(1, $asistente->getId_nom());
        $this->assertTrue($asistente->isPropio());
        $this->assertTrue($asistente->isEst_ok());
        $this->assertTrue($asistente->isCfi());
        $this->assertEquals(1, $asistente->getCfi_con());
        $this->assertTrue($asistente->isFalta());
        $this->assertEquals('test', $asistente->getEncargoVo()->value());
        $this->assertEquals('Test', $asistente->getDlResponsableVo()->value());
        $this->assertEquals('test', $asistente->getObservVo()->value());
        $this->assertEquals('Test', $asistente->getIdTablaVo()->value());
        $this->assertEquals(1, $asistente->getPlazaVo()->value());
        $this->assertEquals('test', $asistente->getPropietarioVo()->value());
        $this->assertEquals('test', $asistente->getObservEstVo()->value());
    }
}
