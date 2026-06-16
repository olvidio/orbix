<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asistentes\application\AsistenteEliminar;
use src\asistentes\application\AsistenteGuardar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;

final class AsistenteGuardarTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    private function createSut(
        ?AsistenteApplicationService $app = null,
        ?DossierRepositoryInterface $dosRepo = null,
        ?AsistenteEliminar $asistenteEliminar = null,
        ?PlazaPropietarioAsignacionInterface $plazaPropietario = null,
    ): AsistenteGuardar {
        $app ??= $this->createMock(AsistenteApplicationService::class);
        $dosRepo ??= $this->createMock(DossierRepositoryInterface::class);
        $asistenteEliminar ??= new AsistenteEliminar(
            $this->createMock(AsistenteApplicationService::class),
            $this->createMock(MatriculaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
        );
        $plazaPropietario ??= $this->createMock(PlazaPropietarioAsignacionInterface::class);
        return new AsistenteGuardar($app, $dosRepo, $asistenteEliminar, $plazaPropietario);
    }

    public function test_mod_no_soportado(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute(['mod' => 'otro', 'id_activ' => 1, 'id_nom' => 1]));
    }

    public function test_faltan_ids(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute(['mod' => 'nuevo', 'id_activ' => 0, 'id_nom' => 1]));
        $this->assertNotSame('', $sut->execute(['mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 0]));
    }

    public function test_nuevo_acepta_ids_negativos_de_paso(): void
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier)->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(-2001456, -1001123)->willReturn(null);
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) {
            return $a->getId_activ() === -2001456 && $a->getId_nom() === -1001123;
        });

        $sut = $this->createSut($app, $dosRepo);

        $this->assertSame('', $sut->execute([
            'mod' => 'nuevo',
            'id_activ' => -2001456,
            'id_nom' => -1001123,
            'plaza' => 1,
        ]));
    }

    public function test_mover_sin_id_activ_old(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute([
            'mod' => 'mover',
            'id_activ' => 2,
            'id_nom' => 3,
            'id_activ_old' => 0,
        ]));
    }

    public function test_nuevo_abre_dossier_existente_y_guarda(): void
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier)->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(10, 20)->willReturn(null);
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) {
            return $a->getId_activ() === 10 && $a->getId_nom() === 20;
        });

        $sut = $this->createSut($app, $dosRepo);

        $this->assertSame('', $sut->execute([
            'mod' => 'nuevo',
            'id_activ' => 10,
            'id_nom' => 20,
            'plaza' => 1,
        ]));
    }

    public function test_editar_sin_permiso(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(false);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->never())->method('guardar');

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute([
            'mod' => 'editar',
            'id_activ' => 1,
            'id_nom' => 2,
        ]));
    }

    public function test_editar_guarda(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->once())->method('guardar')->with($o)->willReturn(true);

        $sut = $this->createSut($app);

        $this->assertSame('', $sut->execute([
            'mod' => 'editar',
            'id_activ' => 1,
            'id_nom' => 2,
            'encargo' => 'x',
            'plaza' => 1,
        ]));
    }

    public function test_mover_no_elimina_si_falla_guardar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('setEncargo')->willReturnSelf();
        $o->method('setObserv')->willReturnSelf();
        $o->method('setObservEstVo')->willReturnSelf();
        $o->method('setPropio')->willReturnSelf();
        $o->method('setEst_ok')->willReturnSelf();
        $o->method('setCfi')->willReturnSelf();
        $o->method('setFalta')->willReturnSelf();
        $o->method('setCfi_con')->willReturnSelf();
        $o->method('setDlResponsableVo')->willReturnSelf();
        $o->method('setPropietarioVo')->willReturnSelf();
        $o->method('setPlazaVoComprobando')->willReturn('Ya están todas las plazas ocupadas');

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(20, 10)->willReturn($o);
        $app->expects($this->never())->method('guardar');

        // AsistenteEliminar es final (no mockeable): usamos uno real cuyo colaborador
        // demuestra que execute() NO se invoca (no llega a buscar ni eliminar nada).
        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->expects($this->never())->method('findById');
        $elimApp->expects($this->never())->method('eliminar');
        $eliminar = new AsistenteEliminar(
            $elimApp,
            $this->createMock(MatriculaRepositoryInterface::class),
            $this->createMock(DossierRepositoryInterface::class),
        );

        $sut = $this->createSut($app, null, $eliminar);

        $this->assertSame(
            'Ya están todas las plazas ocupadas',
            $sut->execute([
                'mod' => 'mover',
                'id_activ' => 20,
                'id_nom' => 10,
                'id_activ_old' => 5,
                'plaza' => 4,
            ])
        );
    }

    public function test_mover_elimina_solo_despues_de_guardar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('setEncargo')->willReturnSelf();
        $o->method('setObserv')->willReturnSelf();
        $o->method('setObservEstVo')->willReturnSelf();
        $o->method('setPropio')->willReturnSelf();
        $o->method('setEst_ok')->willReturnSelf();
        $o->method('setCfi')->willReturnSelf();
        $o->method('setFalta')->willReturnSelf();
        $o->method('setCfi_con')->willReturnSelf();
        $o->method('setDlResponsableVo')->willReturnSelf();
        $o->method('setPropietarioVo')->willReturnSelf();
        $o->method('setPlazaVoComprobando')->willReturn('');

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(20, 10)->willReturn($o);
        $app->expects($this->once())->method('guardar')->with($o)->willReturn(true);

        // AsistenteEliminar es final (no mockeable): usamos uno real y verificamos a
        // través de sus colaboradores que se elimina el origen (id_activ_old=5, id_nom=10)
        // sólo tras un guardado correcto.
        $oOrigen = $this->createMock(Asistente::class);
        $oOrigen->method('perm_modificar')->willReturn(true);

        $elimApp = $this->createMock(AsistenteApplicationService::class);
        $elimApp->method('findById')->with(5, 10)->willReturn($oOrigen);
        $elimApp->expects($this->once())->method('eliminar')->with($oOrigen)->willReturn(true);

        $elimMatRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $elimMatRepo->method('getMatriculas')->willReturn([]);

        $elimDosRepo = $this->createMock(DossierRepositoryInterface::class);
        $elimDosRepo->method('findByPk')->willReturn(null);

        $eliminar = new AsistenteEliminar($elimApp, $elimMatRepo, $elimDosRepo);

        $sut = $this->createSut($app, null, $eliminar);

        $this->assertSame('', $sut->execute([
            'mod' => 'mover',
            'id_activ' => 20,
            'id_nom' => 10,
            'id_activ_old' => 5,
            'plaza' => 4,
        ]));
    }
}
