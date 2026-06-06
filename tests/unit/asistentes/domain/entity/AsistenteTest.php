<?php

namespace Tests\unit\asistentes\domain\entity;

use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\PlazaPropietarioAsignacion;
use src\asistentes\domain\entity\Asistente;
use src\shared\config\ConfigGlobal;
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

    public function test_setPlazaVoComprobando_sin_propietario_asigna_primera_libre(): void
    {
        $configBackup = $this->instalarAppActividadPlazas();

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->expects($this->once())
            ->method('getPrimeraPropiedadLibre')
            ->with(false)
            ->willReturn('dlv>dlv');
        $asignacion = new PlazaPropietarioAsignacion($resumenSvc);

        try {
            $this->Asistente->setId_activ(10);
            $err = $this->Asistente->setPlazaVoComprobando(PlazaId::ASIGNADA, $asignacion);

            $this->assertSame('', $err);
            $this->assertSame(PlazaId::ASIGNADA, $this->Asistente->getPlazaVo()->value());
            $this->assertSame('dlv>dlv', $this->Asistente->getPropietarioVo()->value());
        } finally {
            $this->restaurarConfigApps($configBackup);
        }
    }

    public function test_setPlazaVoComprobando_sin_propietario_ni_libres_devuelve_error(): void
    {
        $configBackup = $this->instalarAppActividadPlazas();

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->method('getPrimeraPropiedadLibre')->willReturn(null);
        $asignacion = new PlazaPropietarioAsignacion($resumenSvc);

        try {
            $this->Asistente->setId_activ(10);
            $err = $this->Asistente->setPlazaVoComprobando(PlazaId::ASIGNADA, $asignacion);

            $this->assertNotSame('', $err);
            $this->assertSame(PlazaId::ASIGNADA, $this->Asistente->getPlazaVo()->value());
        } finally {
            $this->restaurarConfigApps($configBackup);
        }
    }

    public function test_setPlazaVoComprobando_con_propietario_mantiene_asignada(): void
    {
        $configBackup = $this->instalarAppActividadPlazas();

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->expects($this->once())
            ->method('esPropiedadClaveDisponible')
            ->with('dlA>dlB', false)
            ->willReturn(true);
        $resumenSvc->expects($this->never())->method('getPrimeraPropiedadLibre');
        $asignacion = new PlazaPropietarioAsignacion($resumenSvc);

        try {
            $this->Asistente->setId_activ(10);
            $this->Asistente->setPropietarioVo('dlA>dlB');
            $err = $this->Asistente->setPlazaVoComprobando(PlazaId::ASIGNADA, $asignacion);

            $this->assertSame('', $err);
            $this->assertSame(PlazaId::ASIGNADA, $this->Asistente->getPlazaVo()->value());
            $this->assertSame('dlA>dlB', $this->Asistente->getPropietarioVo()->value());
        } finally {
            $this->restaurarConfigApps($configBackup);
        }
    }

    public function test_setPlazaVoComprobando_con_propietario_sin_plazas_devuelve_error(): void
    {
        $configBackup = $this->instalarAppActividadPlazas();

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->expects($this->once())
            ->method('esPropiedadClaveDisponible')
            ->with('dlA>dlB', false)
            ->willReturn(false);
        $resumenSvc->expects($this->never())->method('getPrimeraPropiedadLibre');
        $asignacion = new PlazaPropietarioAsignacion($resumenSvc);

        try {
            $this->Asistente->setId_activ(10);
            $this->Asistente->setPropietarioVo('dlA>dlB');
            $err = $this->Asistente->setPlazaVoComprobando(PlazaId::ASIGNADA, $asignacion);

            $this->assertNotSame('', $err);
            $this->assertSame(PlazaId::ASIGNADA, $this->Asistente->getPlazaVo()->value());
        } finally {
            $this->restaurarConfigApps($configBackup);
        }
    }

    public function test_setPlazaVoComprobando_ya_asignada_no_revalida_plazas(): void
    {
        $configBackup = $this->instalarAppActividadPlazas();

        $resumenSvc = $this->createMock(ResumenPlazasService::class);
        $resumenSvc->expects($this->never())->method('esPropiedadClaveDisponible');
        $resumenSvc->expects($this->never())->method('getPrimeraPropiedadLibre');
        $asignacion = new PlazaPropietarioAsignacion($resumenSvc);

        try {
            $this->Asistente->setId_activ(10);
            $this->Asistente->setPlazaVo(PlazaId::ASIGNADA);
            $this->Asistente->setPropietarioVo('dlA>dlB');
            $err = $this->Asistente->setPlazaVoComprobando(PlazaId::CONFIRMADA, $asignacion);

            $this->assertSame('', $err);
            $this->assertSame(PlazaId::CONFIRMADA, $this->Asistente->getPlazaVo()->value());
        } finally {
            $this->restaurarConfigApps($configBackup);
        }
    }

    /**
     * @return array{a_apps: array, app_installed: array}
     */
    private function instalarAppActividadPlazas(): array
    {
        $backup = [
            'a_apps' => $_SESSION['config']['a_apps'] ?? [],
            'app_installed' => $_SESSION['config']['app_installed'] ?? [],
        ];
        $_SESSION['config']['a_apps']['actividadplazas'] = 99001;
        $_SESSION['config']['app_installed'] = array_values(array_unique(array_merge(
            $_SESSION['config']['app_installed'] ?? [],
            [99001]
        )));
        $this->assertTrue(ConfigGlobal::is_app_installed('actividadplazas'));

        return $backup;
    }

    /**
     * @param array{a_apps: array, app_installed: array} $backup
     */
    private function restaurarConfigApps(array $backup): void
    {
        $_SESSION['config']['a_apps'] = $backup['a_apps'];
        $_SESSION['config']['app_installed'] = $backup['app_installed'];
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
