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
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_faltan_ids(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $this->createMock(AsistenteApplicationService::class),
            MatriculaRepositoryInterface::class => $this->createMock(MatriculaRepositoryInterface::class),
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', AsistenteEliminar::execute(['id_activ' => 0, 'id_nom' => 1]));
    }

    public function test_no_encontrado(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(10, 20)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            MatriculaRepositoryInterface::class => $this->createMock(MatriculaRepositoryInterface::class),
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', AsistenteEliminar::execute(['id_activ' => 10, 'id_nom' => 20]));
    }

    public function test_sin_permiso_modificar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(false);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->never())->method('eliminar');

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            MatriculaRepositoryInterface::class => $this->createMock(MatriculaRepositoryInterface::class),
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', AsistenteEliminar::execute(['id_activ' => 1, 'id_nom' => 2]));
    }

    public function test_falla_eliminar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->method('eliminar')->willReturn(false);

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            MatriculaRepositoryInterface::class => $this->createMock(MatriculaRepositoryInterface::class),
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', AsistenteEliminar::execute(['id_activ' => 1, 'id_nom' => 2]));
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

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            MatriculaRepositoryInterface::class => $matRepo,
            DossierRepositoryInterface::class => $dosRepo,
        ]);

        $msg = AsistenteEliminar::execute([
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

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            MatriculaRepositoryInterface::class => $matRepo,
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', AsistenteEliminar::execute(['id_activ' => 1, 'id_nom' => 2]));
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
