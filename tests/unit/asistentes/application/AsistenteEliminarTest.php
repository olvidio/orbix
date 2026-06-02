<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\asistentes\application\AsistenteEliminar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;

final class AsistenteEliminarTest extends TestCase
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
        ?MatriculaRepositoryInterface $matRepo = null,
        ?DossierRepositoryInterface $dosRepo = null,
    ): AsistenteEliminar {
        $app ??= $this->createMock(AsistenteApplicationService::class);
        $matRepo ??= $this->createMock(MatriculaRepositoryInterface::class);
        $dosRepo ??= $this->createMock(DossierRepositoryInterface::class);
        return new AsistenteEliminar($app, $matRepo, $dosRepo);
    }

    public function test_faltan_ids(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute(['id_activ' => 0, 'id_nom' => 1]));
    }

    public function test_no_encontrado(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(10, 20)->willReturn(null);

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute(['id_activ' => 10, 'id_nom' => 20]));
    }

    public function test_sin_permiso_modificar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(false);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->never())->method('eliminar');

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute(['id_activ' => 1, 'id_nom' => 2]));
    }

    public function test_falla_eliminar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->method('eliminar')->willReturn(false);

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute(['id_activ' => 1, 'id_nom' => 2]));
    }

    public function test_exito_sin_matriculas_y_parse_sel_p(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(7, 3)->willReturn($o);
        $app->method('eliminar')->with($o)->willReturn(true);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->with(['id_activ' => 7, 'id_nom' => 3])->willReturn([]);

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn(null);

        $sut = $this->createSut($app, $matRepo, $dosRepo);

        $msg = $sut->execute([
            'pau' => 'p',
            'sel' => ['7#'],
            'id_pau' => 3,
        ]);
        $this->assertSame('', $msg);
    }

    public function test_error_si_dbeeliminar_matricula_falla(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->method('eliminar')->willReturn(true);

        $m = $this->getMockBuilder(Matricula::class)->addMethods(['DBEliminar'])->getMock();
        $m->method('DBEliminar')->willReturn(false);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([$m]);

        $sut = $this->createSut($app, $matRepo);

        $this->assertNotSame('', $sut->execute(['id_activ' => 1, 'id_nom' => 2]));
    }
}
